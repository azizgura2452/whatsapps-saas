<?php

namespace App\Services\WhatsApp;

use App\Models\Business;
use App\Models\ChatFlowStep;
use App\Services\CustomerService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class DynamicFlowService
{
    protected $whatsAppService;
    protected $stateService;
    protected $customerService;
    protected $orderService;

    public function __construct(
        WhatsAppService $whatsAppService,
        ConversationStateService $stateService,
        CustomerService $customerService,
        OrderService $orderService
    ) {
        $this->whatsAppService = $whatsAppService;
        $this->stateService = $stateService;
        $this->customerService = $customerService;
        $this->orderService = $orderService;
    }

    /**
     * Process incoming message through dynamic flow
     */
    public function processMessage(array $messageData, Business $business)
    {
        $from = $messageData['from'];
        $currentState = $this->stateService->getCurrentState($from);
        
        // Store business context in state
        $this->stateService->addStateData($from, ['business_id' => $business->id]);
        
        // Set locale based on user's language preference
        $this->setLocale($from);

        // Get active flow steps for this business
        $flowSteps = $business->getActiveFlowSteps();

        if ($flowSteps->isEmpty()) {
            Log::warning("No active flow steps for business: {$business->id}");
            return $this->whatsAppService->sendText(
                $from,
                $business,
                "Service temporarily unavailable. Please try again later."
            );
        }

        // Handle different message types
        if ($messageData['buttonReplyId']) {
            return $this->handleButtonReply($messageData['buttonReplyId'], $from, $business, $flowSteps);
        }

        if ($messageData['listReplyId']) {
            return $this->handleListReply($messageData['listReplyId'], $from, $business, $flowSteps);
        }

        if ($messageData['type'] === 'order') {
            return $this->handleOrderMessage($messageData, $from, $business);
        }

        if (!empty($messageData['text'])) {
            return $this->handleTextMessage($messageData['text'], $from, $business, $flowSteps);
        }

        return null;
    }

    /**
     * Handle text message through flow
     */
    protected function handleTextMessage(string $text, string $from, Business $business, $flowSteps)
    {
        $text = strtolower(trim($text));
        $currentState = $this->stateService->getCurrentState($from);
        $currentStepId = $currentState['data']['current_step_id'] ?? null;

        // Check for restart/reset commands
        if (in_array($text, ['restart', 'reset'])) {
            $this->stateService->resetState($from);
            return $this->executeFirstStep($from, $business, $flowSteps);
        }

        // Find matching trigger in current or any step
        $nextStep = null;

        // First, check triggers in current step if we have one
        if ($currentStepId) {
            $currentStep = $flowSteps->firstWhere('id', $currentStepId);
            if ($currentStep) {
                foreach ($currentStep->triggers as $trigger) {
                    if ($trigger->trigger_type === 'text' && $trigger->matches($text)) {
                        $nextStep = $flowSteps->firstWhere('id', $trigger->next_step_id);
                        break;
                    }
                }
            }
        }

        // If no match in current step, check all steps for global triggers
        if (!$nextStep) {
            foreach ($flowSteps as $step) {
                foreach ($step->triggers as $trigger) {
                    if ($trigger->trigger_type === 'text' && $trigger->matches($text)) {
                        $nextStep = $flowSteps->firstWhere('id', $trigger->next_step_id);
                        break 2;
                    }
                }
            }
        }

        // If we found a next step, execute it
        if ($nextStep) {
            return $this->executeStep($nextStep, $from, $business);
        }

        // Handle state-specific text input (like collecting name, address, etc.)
        return $this->handleStateSpecificInput($text, $from, $business, $flowSteps, $currentState);
    }

    /**
     * Handle button reply through flow
     */
    protected function handleButtonReply(string $buttonId, string $from, Business $business, $flowSteps)
    {
        $currentState = $this->stateService->getCurrentState($from);
        $currentStepId = $currentState['data']['current_step_id'] ?? null;

        // Find trigger matching this button reply
        $nextStep = null;

        if ($currentStepId) {
            $currentStep = $flowSteps->firstWhere('id', $currentStepId);
            if ($currentStep) {
                foreach ($currentStep->triggers as $trigger) {
                    if ($trigger->trigger_type === 'button_reply' && $trigger->matches($buttonId)) {
                        $nextStep = $flowSteps->firstWhere('id', $trigger->next_step_id);
                        break;
                    }
                }
            }
        }

        if ($nextStep) {
            return $this->executeStep($nextStep, $from, $business);
        }

        return $this->whatsAppService->sendText(
            $from,
            $business,
            "I didn't understand that selection. Please try again."
        );
    }

    /**
     * Handle list reply through flow
     */
    protected function handleListReply(string $listId, string $from, Business $business, $flowSteps)
    {
        $currentState = $this->stateService->getCurrentState($from);
        $currentStepId = $currentState['data']['current_step_id'] ?? null;

        // Handle category selection
        if (str_starts_with($listId, 'cat_')) {
            $categoryId = substr($listId, 4);
            $this->stateService->addStateData($from, ['selected_category' => $categoryId]);
            return $this->whatsAppService->sendProductsFromCategory($from, $business, $categoryId);
        }

        // Handle product selection
        if (str_starts_with($listId, 'prod_')) {
            $productId = substr($listId, 5);
            $this->stateService->addStateData($from, ['selected_product' => $productId]);
            return $this->whatsAppService->sendSingleProduct($from, $business, $productId);
        }

        // Find trigger matching this list reply
        $nextStep = null;

        if ($currentStepId) {
            $currentStep = $flowSteps->firstWhere('id', $currentStepId);
            if ($currentStep) {
                foreach ($currentStep->triggers as $trigger) {
                    if ($trigger->trigger_type === 'list_reply' && $trigger->matches($listId)) {
                        $nextStep = $flowSteps->firstWhere('id', $trigger->next_step_id);
                        break;
                    }
                }
            }
        }

        if ($nextStep) {
            return $this->executeStep($nextStep, $from, $business);
        }

        return $this->whatsAppService->sendText(
            $from,
            $business,
            "I didn't understand that selection. Please try again."
        );
    }

    /**
     * Handle order message
     */
    protected function handleOrderMessage(array $messageData, string $from, Business $business)
    {
        try {
            $orderData = $messageData['raw']['order'];
            
            // Store order data in state
            $this->stateService->addStateData($from, ['order' => $orderData]);

            // Find the order processing step
            $orderStep = $business->flowSteps()
                ->where('step_type', 'order_processing')
                ->where('is_active', true)
                ->first();

            if ($orderStep) {
                return $this->executeStep($orderStep, $from, $business);
            }

            // Fallback: ask for name
            return $this->askForName($from, $business);

        } catch (\Exception $e) {
            Log::error('Order processing failed: ' . $e->getMessage());
            return $this->whatsAppService->sendText(
                $from,
                $business,
                'We encountered an error processing your order. Please try again.'
            );
        }
    }

    /**
     * Execute the first step in the flow
     */
    protected function executeFirstStep(string $from, Business $business, $flowSteps)
    {
        $firstStep = $flowSteps->first();
        
        if ($firstStep) {
            return $this->executeStep($firstStep, $from, $business);
        }

        return $this->whatsAppService->sendText(
            $from,
            $business,
            "Welcome! How can we help you today?"
        );
    }

    /**
     * Execute a specific flow step
     */
    protected function executeStep(ChatFlowStep $step, string $from, Business $business)
    {
        // Update current step in state
        $this->stateService->addStateData($from, ['current_step_id' => $step->id]);

        $language = $this->stateService->getLanguage($from);
        $message = $step->getMessageForLanguage($language);

        if (!$message) {
            $message = $step->getMessageForLanguage('english');
        }

        if (!$message) {
            Log::error("No message found for step {$step->id}");
            return null;
        }

        // Execute based on step type
        switch ($step->step_type) {
            case 'welcome':
                return $this->executeWelcomeStep($message, $from, $business);
            
            case 'language_selection':
                return $this->executeLanguageStep($message, $from, $business);
            
            case 'menu':
                return $this->executeMenuStep($message, $from, $business);
            
            case 'catalog':
                return $this->executeCatalogStep($message, $from, $business);
            
            case 'support':
                return $this->executeSupportStep($message, $from, $business);
            
            case 'collect_name':
                return $this->executeCollectNameStep($message, $from, $business);
            
            case 'collect_address':
                return $this->executeCollectAddressStep($message, $from, $business);
            
            case 'payment_confirmation':
                return $this->executePaymentConfirmationStep($message, $from, $business);
            
            case 'custom':
                return $this->executeCustomStep($message, $from, $business, $step);
            
            default:
                return $this->executeGenericStep($message, $from, $business);
        }
    }

    /**
     * Execute welcome step
     */
    protected function executeWelcomeStep($message, string $from, Business $business)
    {
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute language selection step
     */
    protected function executeLanguageStep($message, string $from, Business $business)
    {
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute menu step
     */
    protected function executeMenuStep($message, string $from, Business $business)
    {
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute catalog step
     */
    protected function executeCatalogStep($message, string $from, Business $business)
    {
        return $this->whatsAppService->sendCatalogCategories($from, $business);
    }

    /**
     * Execute support step
     */
    protected function executeSupportStep($message, string $from, Business $business)
    {
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute collect name step
     */
    protected function executeCollectNameStep($message, string $from, Business $business)
    {
        $this->stateService->setState($from, 'collecting_name');
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute collect address step
     */
    protected function executeCollectAddressStep($message, string $from, Business $business)
    {
        $this->stateService->setState($from, 'collecting_address');
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute payment confirmation step
     */
    protected function executePaymentConfirmationStep($message, string $from, Business $business)
    {
        // Get order data and build confirmation message
        $stateData = $this->stateService->getCurrentState($from);
        $orderData = $stateData['data']['order'] ?? [];
        
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute custom step
     */
    protected function executeCustomStep($message, string $from, Business $business, ChatFlowStep $step)
    {
        // Custom steps can have additional logic in config
        $config = $step->config ?? [];
        
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Execute generic step
     */
    protected function executeGenericStep($message, string $from, Business $business)
    {
        return $this->sendMessageByType($message, $from, $business);
    }

    /**
     * Send message based on type
     */
    protected function sendMessageByType($message, string $from, Business $business)
    {
        switch ($message->message_type) {
            case 'text':
                return $this->whatsAppService->sendText(
                    $from,
                    $business,
                    $message->message_content
                );
            
            case 'buttons':
                return $this->whatsAppService->sendButtons(
                    $from,
                    $business,
                    $message->message_content,
                    $message->buttons ?? []
                );
            
            case 'list':
                return $this->whatsAppService->sendList(
                    $from,
                    $business,
                    $message->message_content,
                    $message->list_sections ?? []
                );
            
            case 'template':
                return $this->whatsAppService->sendTemplate(
                    $from,
                    $business,
                    $message->template_name,
                    []
                );
            
            default:
                return $this->whatsAppService->sendText(
                    $from,
                    $business,
                    $message->message_content
                );
        }
    }

    /**
     * Handle state-specific input (like name, address collection)
     */
    protected function handleStateSpecificInput(string $text, string $from, Business $business, $flowSteps, array $currentState)
    {
        $state = $currentState['state'] ?? 'initial';
        $currentStepId = $currentState['data']['current_step_id'] ?? null;

        switch ($state) {
            case 'collecting_name':
                $this->stateService->addStateData($from, ['customer_name' => $text]);
                
                // Find next step (should be collect_address)
                $nextStep = $flowSteps->firstWhere('step_type', 'collect_address');
                if ($nextStep) {
                    return $this->executeStep($nextStep, $from, $business);
                }
                break;

            case 'collecting_address':
                $this->stateService->addStateData($from, ['customer_address' => $text]);
                
                // Update customer
                $customerName = $currentState['data']['customer_name'] ?? 'Customer';
                $customer = $this->customerService->getOrCreateCustomer([
                    'whatsapp_number' => $from,
                    'business_id' => $business->id
                ]);
                
                $customer->update([
                    'name' => $customerName,
                    'address' => $text
                ]);
                
                $this->stateService->addStateData($from, ['customer_id' => $customer->id]);
                
                // Find next step (should be payment_confirmation)
                $nextStep = $flowSteps->firstWhere('step_type', 'payment_confirmation');
                if ($nextStep) {
                    return $this->executeStep($nextStep, $from, $business);
                }
                break;
        }

        // Default: send help message
        return $this->whatsAppService->sendText(
            $from,
            $business,
            "I'm not sure what you need. Type 'restart' to begin again."
        );
    }

    /**
     * Helper: Ask for name
     */
    protected function askForName(string $from, Business $business)
    {
        $this->stateService->setState($from, 'collecting_name');
        return $this->whatsAppService->sendText(
            $from,
            $business,
            __('PleaseEnterYourFullName')
        );
    }

    /**
     * Set locale based on user preference
     */
    protected function setLocale(string $from)
    {
        $lang = $this->stateService->getLanguage($from);
        App::setLocale($lang === 'arabic' ? 'ar' : 'en');
    }
}