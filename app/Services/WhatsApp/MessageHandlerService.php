<?php

namespace App\Services\WhatsApp;

use App\Enums\OrderStatus;
use App\Services\SettingService;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Services\CustomerService;
use App\Services\OrderService;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use Illuminate\Support\Facades\App;
use Exception;

class MessageHandlerService
{
    protected $whatsAppService;
    protected $stateService;
    protected $customerService;
    protected $orderService;
    protected $settingService;

    public function __construct(
        WhatsAppService $whatsAppService,
        ConversationStateService $stateService,
        CustomerService $customerService,
        OrderService $orderService,
        SettingService $settingService
    ) {
        $this->whatsAppService = $whatsAppService;
        $this->stateService = $stateService;
        $this->customerService = $customerService;
        $this->orderService = $orderService;
        $this->settingService = $settingService;
    }

    public function setLocale($to)
    {
        $lang = $this->stateService->getLanguage(($to));
        App::setLocale($lang === 'arabic' ? 'ar' : 'en');
    }
    /**
     * Handle text message based on content and current state
     */
    public function handleTextMessage(string $from, string $text)
    {
        $currentState = $this->stateService->getCurrentState($from);

        // Normalize text
        $text = strtolower(trim($text));

        // Handle universal commands regardless of state
        if (in_array($text, ['restart', 'reset'])) {
            $this->stateService->resetState($from);
            return $this->sendWelcomeMessage($from);
        }

        $greetings = [
            // English greetings
            'hi',
            'hii',
            'hiii',
            'hello',
            'hey',
            'heyy',
            'heyyy',
            'start',
            'get started',
            'getstarted',
            'restart',
            'begin',
            'good morning',
            'good afternoon',
            'good evening',
            'yo',
            'sup',
            'whats up',
            "what's up",
            'how are you',
            'hi there',
            'hello there',
            'greetings',

            // Arabic greetings
            'السلام عليكم',
            'السلام',
            'هلا',
            'هلا والله',
            'هلاو',
            'مرحبا',
            'مرحبا بك',
            'أهلاً',
            'أهلاً وسهلاً',
            'أهلا',
            'صباح الخير',
            'مساء الخير',
            'كيف الحال',
            'شلونك',
            'شخبارك',
            'ابدأ',
            'ابدا',
            'أبدأ',
            'ابداء',
            'ابدء',
            'ارجع',
            'إعادة',
            'إبدا',
            'يلا نبدأ',
            'نبدأ',
        ];

        if (in_array($text, $greetings)) {
            $this->stateService->resetState($from); // Reset state for a new session
            return $this->sendLanguageSelection($from);
        }

        // Handle commands based on current state
        switch ($currentState['state']) {
            case ConversationStateService::STATE_INITIAL:
                // if ($text === 'hi' || $text === 'hello' || $text === 'start' || $text === 'hii' || $text === 'heyy' || $text === 'السلام عليكم' || $text === 'السلام') {
                //     return $this->sendLanguageSelection($from);
                // }
                return $this->sendWelcomeMessage($from);

            case ConversationStateService::STATE_LANGUAGE_SELECTED:
                return $this->sendMenuButton($from);

            case ConversationStateService::STATE_NAME_REQUESTED:
                // Store the provided name
                $this->stateService->setState(
                    $from,
                    ConversationStateService::STATE_NAME_PROVIDED,
                    ['customer_name' => $text]
                );
                return $this->requestAddress($from);

            case ConversationStateService::STATE_ADDRESS_REQUESTED:
                // Store the provided address
                $this->stateService->setState(
                    $from,
                    ConversationStateService::STATE_ADDRESS_PROVIDED,
                    ['customer_address' => $text]
                );

                $customerAddress = $text;

                try {
                    $customer = $this->customerService->findByWhatsapp($from);

                    if ($customer) {
                        // Update only the address
                        $customer->update(['address' => $customerAddress]);
                    } else {
                        // Optionally handle if customer doesn't exist
                        Log::warning("Customer with WhatsApp {$from} not found.");
                    }

                    // Store customer ID in state
                    $this->stateService->addStateData($from, ['customer_id' => $customer->id]);

                    return $this->askPaymentConfirmation($from);
                } catch (\Exception $e) {
                    Log::error('Failed to create customer: ' . $e->getMessage());
                    $this->stateService->resetState($from);
                    return $this->whatsAppService->sendText(
                        $from,
                        'We encountered an error processing your information. Please try again.'
                    );
                }

            case ConversationStateService::STATE_PAYMENT_REQUESTED:
                if ($text === 'yes') {
                    return $this->sendPaymentLink($from);
                } elseif ($text === 'no') {
                    $this->stateService->resetState($from);
                    return $this->whatsAppService->sendText(
                        $from,
                        __('OrderCancelled')
                    );
                }
                return $this->whatsAppService->sendText(
                    $from,
                    'Please reply with "yes" to proceed with payment or "no" to cancel'
                );

            case ConversationStateService::STATE_SUPPORT_STARTED:
                // Handle support queries
                $this->stateService->addStateData($from, ['support_query' => $text]);
                $this->stateService->resetState($from);
                return $this->whatsAppService->sendText(
                    $from,
                    'Thank you for your query. Our support team will contact you shortly.'
                );

            default:
                // For any other text in other states, show help
                $helpMessage = "I'm not sure what you need. You can type 'restart' to begin again or choose an option from the menu.";
                return $this->whatsAppService->sendText($from, $helpMessage);
        }
    }

    /**
     * Handle button reply based on ID and current state
     */
    public function handleButtonReply(string $from, string $buttonId)
    {
        $currentState = $this->stateService->getCurrentState($from);
        $this->setLocale($from);

        switch ($buttonId) {
            case 'english':
            case 'arabic':
                // Set language and update state
                $this->stateService->setState(
                    $from,
                    ConversationStateService::STATE_LANGUAGE_SELECTED,
                    ['language' => $buttonId]
                );
                // Set locale
                $this->setLocale($from);
                return $this->sendMenuButton($from);

            case 'show_catalog':
                $this->stateService->setState($from, ConversationStateService::STATE_CATALOG_SHOWN);
                return $this->sendCatalogCategories($from);

            case 'place_order':
                $this->stateService->setState($from, ConversationStateService::STATE_ORDER_STARTED);
                return $this->askName($from);

            case 'support':
                $this->stateService->setState($from, ConversationStateService::STATE_SUPPORT_STARTED);
                return $this->whatsAppService->sendButtons($from, __('SupportPrompt'), [
                    ['type' => 'reply', 'reply' => ['id' => 'refunds', 'title' => __('Refunds')]],
                    ['type' => 'reply', 'reply' => ['id' => 'complaints', 'title' => __('Complaints')]],
                    ['type' => 'reply', 'reply' => ['id' => 'help', 'title' => __('Help')]]
                ]);

            case 'confirm_payment':
                return $this->sendPaymentLink($from);

            case 'cancel_order':
                $this->stateService->resetState($from);
                return $this->whatsAppService->sendText($from, __('OrderCancelled'));

            case 'refunds':
                return $this->whatsAppService->sendText($from, __('ContactSupport', ['number' => '+965 9300 9009']));

            case 'complaints':
                return $this->whatsAppService->sendText($from, __('DescribeComplaint', ['number' => '+965 9300 9009']));

            case 'help':
                return $this->whatsAppService->sendText($from, __('DescribeIssue', ['number' => '+965 9300 9009']));

            default:
                Log::info('Unknown button reply: ' . $buttonId);
                return $this->whatsAppService->sendText($from, __('UnknownSelection'));
        }
    }

    /**
     * Handle list reply based on ID and current state
     */
    public function handleListReply(string $from, string $listId)
    {
        $currentState = $this->stateService->getCurrentState($from);

        if (str_starts_with($listId, 'cat_')) {
            $categoryId = substr($listId, 4);
            $this->stateService->setState(
                $from,
                ConversationStateService::STATE_CATEGORY_SELECTED,
                ['selected_category' => $categoryId]
            );
            return $this->sendProductsFromCategory($from, $categoryId);
        } elseif (str_starts_with($listId, 'prod_')) {
            $productId = substr($listId, 5);
            $this->stateService->setState(
                $from,
                ConversationStateService::STATE_PRODUCT_SELECTED,
                ['selected_product' => $productId]
            );
            return $this->whatsAppService->sendSingleProduct($from, $productId);
        }

        return $this->whatsAppService->sendText(
            $from,
            'I did not understand that selection. Please try again or type "restart" to begin again.'
        );
    }

    /**
     * Handle order message with proper state tracking
     */
    /**
     * Handle order message with proper state tracking
     */
    public function handleOrderMessage(string $from, array $orderData, CustomerService $customerService, OrderService $orderService)
    {
        try {
            $orderTotal = array_sum(array_map(fn($item) => $item['item_price'] * $item['quantity'], $orderData['product_items']));

            // Store order data in the conversation state
            $this->stateService->setState(
                $from,
                ConversationStateService::STATE_ORDER_STARTED,
                ['order' => $orderData]
            );

            // Log::info('Order received, starting name collection', ['from' => $from]);

            // Start by asking for the customer's name
            return $this->askName($from);

        } catch (\Exception $e) {
            Log::error('Order processing failed: ' . $e->getMessage());
            $this->whatsAppService->sendText($from, 'We encountered an error processing your order. Our team will contact you.');
            $this->stateService->resetState($from);
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Send welcome message
     */
    public function sendWelcomeMessage(string $to)
    {
        $this->stateService->setState($to, ConversationStateService::STATE_INITIAL);
        return $this->whatsAppService->sendText($to, "Welcome to our varsityheadwear! Type \"hi\" to get started. \nمرحبًا بك في فارسيتي هيدوير! اكتب \"hi\" للبدء.");
    }

    /**
     * Send language selection buttons
     */
    public function sendLanguageSelection(string $to)
    {
        $this->stateService->setState($to, ConversationStateService::STATE_INITIAL);

        return $this->whatsAppService->sendButtons($to, "Welcome to varsityheadwear Kuwait! Kindly choose your language.
        مرحبًا بك في فارسيتي فرع الكويت ! يرجى اختيار لغتك", [
            ['type' => 'reply', 'reply' => ['id' => 'english', 'title' => 'English']],
            ['type' => 'reply', 'reply' => ['id' => 'arabic', 'title' => 'Arabic']]
        ], 'Language/ اللغة');

    }

    /**
     * Send menu buttons
     */
    public function sendMenuButton(string $to)
    {
        $this->stateService->setState($to, ConversationStateService::STATE_MENU_SHOWN);
        return $this->whatsAppService->sendButtons(
            $to,
            __('Welcome to VarsityHeadWear Kuwait! How can we assist you today? Select from list'),
            [
                ['type' => 'reply', 'reply' => ['id' => 'show_catalog', 'title' => __('Show Products')]],
                ['type' => 'reply', 'reply' => ['id' => 'support', 'title' => __('Get Support')]],
            ],
            __('FAQ')
        );
    }

    /**
     * Send catalog categories as a list
     */
    public function sendCatalogCategories(string $to)
    {
        $categories = $this->whatsAppService->fetchCatalogCategories();

        if (empty($categories)) {
            return $this->whatsAppService->sendText($to, 'Unable to load catalog categories.');
        }

        $limitedCategories = array_slice($categories, 0, 10);

        $sections = [
            [
                'title' => 'Select a category',
                'rows' => array_map(function ($category) {
                    return [
                        'id' => 'cat_' . $category['id'],
                        'title' => $category['name']
                    ];
                }, $limitedCategories)
            ]
        ];

        $this->stateService->setState($to, ConversationStateService::STATE_CATALOG_SHOWN);
        $this->setLocale($to);
        return $this->whatsAppService->sendList(
            $to,
            __('Product Categories'),
            __('Choose One Of The Categories Below'),
            $sections
        );
    }

    /**
     * Send products from a specific category
     */
    public function sendProductsFromCategory(string $to, string $categoryId)
    {
        $products = $this->whatsAppService->fetchProductsFromCategory($categoryId);

        if (empty($products)) {
            return $this->whatsAppService->sendText($to, 'No products found in this category.');
        }

        // Log::info('Sending products from category: ' . $categoryId, $products);
        $this->stateService->setState(
            $to,
            ConversationStateService::STATE_CATEGORY_SELECTED,
            ['selected_category' => $categoryId]
        );
        return $this->whatsAppService->sendProductTemplate($to, $products);
    }

    /**
     * Ask for customer name
     */
    private function askName(string $to)
    {
        $this->stateService->setState($to, ConversationStateService::STATE_NAME_REQUESTED);
        $this->setLocale($to);
        return $this->whatsAppService->sendText($to, __('PleaseEnterYourFullName'));
    }

    /**
     * Request address after name is provided
     */
    /**
     * Request address after name is provided
     */
    private function requestAddress(string $to)
    {
        $customerName = $this->stateService->getCurrentState($to)['data']['customer_name'] ?? 'Customer';
        $this->stateService->setState($to, ConversationStateService::STATE_ADDRESS_REQUESTED);
        $displayName = $customerName;
        $this->setLocale($to);

        $customer = $this->customerService->findByWhatsapp($to);

        if ($customer) {
            // Update only the address
            $customer->update(['name' => $customerName]);
        } else {
            // Optionally handle if customer doesn't exist
            Log::warning("Customer with WhatsApp {$to} not found.");
        }

        return $this->whatsAppService->sendText($to, __('ThankYouPleaseProvideAddress', ['name' => $displayName]));
    }

    /**
     * Ask for payment confirmation
     */
    private function askPaymentConfirmation(string $to)
    {
        $stateData = $this->stateService->getCurrentState($to);
        $customerName = $stateData['data']['customer_name'] ?? 'Customer';
        $orderData = $stateData['data']['order'] ?? [];

        // Calculate order total
        $total = 0;
        if (!empty($orderData['product_items'])) {
            $total = array_sum(array_map(function ($item) {
                return $item['item_price'] * $item['quantity'];
            }, $orderData['product_items']));
        }

        $currency = !empty($orderData['product_items']) ?
            ($orderData['product_items'][0]['currency'] ?? 'KWD') : 'KWD';

        $language = $this->stateService->getLanguage($to);
        $localeMap = ['english' => 'en', 'arabic' => 'ar'];
        $locale = $localeMap[$language] ?? 'en';

        $deliveryCharge = $this->settingService->getSetting('delivery_charge');
        $address = $stateData['data']['customer_address'] ?? __('NotProvided', [], $locale); // add "NotProvided" key to lang files if needed
        $displayName = $locale === 'en' ? ucfirst($customerName) : $customerName;

        $message = __('ConfirmOrder', [
            'name' => $displayName,
            'currency' => $currency,
            'total' => number_format($total, 3),
            'address' => $address,
            'delivery_charge' => $deliveryCharge,
        ], $locale);

        // $message = "Thank you, $customerName. Please confirm your order:\n\n";
        // $message .= "Total: $currency " . number_format($total, 3) . "\n";
        // $message .= "Delivery Address: " . ($stateData['data']['customer_address'] ?? 'Not provided') . "\n\n";
        // $message .= "Delivery is Open For Kuwait Only \n";
        // $message .= $currency . " " . $this->settingService->getSetting('delivery_charge') . " Delivery charges will be added. \n";
        // $message .= "Would you like to proceed to payment?";

        $this->stateService->setState($to, ConversationStateService::STATE_PAYMENT_REQUESTED);

        return $this->whatsAppService->sendButtons($to, $message, [
            ['type' => 'reply', 'reply' => ['id' => 'confirm_payment', 'title' => __('YesPayNow', [], $locale)]],
            ['type' => 'reply', 'reply' => ['id' => 'cancel_order', 'title' => __('NoCancel', [], $locale)]]
        ]);
    }

    /**
     * Process order and create in database
     */
    /**
     * Process order and create in database
     */
    private function processOrder(string $to, CustomerService $customerService, OrderService $orderService)
    {
        $stateData = $this->stateService->getCurrentState($to);
        $orderData = $stateData['data']['order'] ?? [];
        $customerId = $stateData['data']['customer_id'] ?? null;

        if (empty($orderData) || empty($customerId)) {
            Log::error("Missing order data for customer $to");
            return $this->whatsAppService->sendText($to, 'Could not find any order. Please try again.');
        }

        try {
            // Create the order payload
            $orderPayload = [
                'customer_id' => $customerId,
                'delivery_charge' => 2.000,
                'currency' => $orderData['product_items'][0]['currency'] ?? 'KWD',
                'status' => OrderStatus::Pending, // Initial status
                'source' => 'whatsapp',
                'notes' => 'Payment pending',
                'items' => array_map(function ($item) {
                    return [
                        'product_id' => Product::where('sku', $item['product_retailer_id'])->value('id'),
                        'quantity' => $item['quantity'],
                        'price' => $item['item_price']
                    ];
                }, $orderData['product_items'] ?? [])
            ];

            // Create order using service
            $order = $orderService->createOrderWithItems($orderPayload);

            // Store order ID in state
            $this->stateService->addStateData($to, ['order_id' => $order->id]);

            return $order;
        } catch (\Exception $e) {
            Log::error('Failed to process order: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Send payment link
     */
    /**
     * Send payment link and process order
     */
    private function sendPaymentLink(string $to)
    {
        // Process the order first
        $order = $this->processOrder($to, $this->customerService, $this->orderService);

        if (!$order) {
            return $this->whatsAppService->sendText($to, 'Failed to process your order. Please try again.');
        }

        // Generate real payment link using MyFatoorahController
        $paymentUrl = $this->generateMyFatoorahInvoiceUrl($order->id);

        if (!$paymentUrl) {
            return $this->whatsAppService->sendText($to, 'Payment link could not be generated. Please try again later.');
        }

        $message = __("Payment Link", ['url' => $paymentUrl]) . "\n\n";
        $message .= __("Order Id", ['id' => $order->id]) . "\n";
        $message .= __("Total Amount", [
            'currency' => $order->currency,
            'amount' => number_format($order->total + $order->delivery_charge, 3)
        ]);


        return $this->whatsAppService->sendText($to, $message);
    }


    private function generateMyFatoorahInvoiceUrl(int $orderId): ?string
    {
        try {
            // Use your existing MyFatoorah config
            $mfConfig = [
                'apiKey' => config('services.myfatoorah.api_key'),
                'isTest' => config('services.myfatoorah.test_mode'),
                'countryCode' => config('services.myfatoorah.country_iso'),
            ];

            // Build payload (similar to getPayLoadData method)
            $order = $this->orderService->getOrderById($orderId);
            if (!$order) {
                return null;
            }

            $payload = [
                'CustomerName' => $order->customer->name ?? 'Customer',
                'InvoiceValue' => $order->total + $order->delivery_charge,
                'DisplayCurrencyIso' => $order->currency,
                'CustomerEmail' => '',
                'CallBackUrl' => route('myfatoorah.callback'),
                'ErrorUrl' => route('myfatoorah.callback'),
                'ReturnUrl' => route('myfatoorah.success', ['order_id' => $orderId]),
                'CustomerMobile' => substr($order->customer->whatsapp_number, 3) ?? '00000000',
                'Language' => 'en',
                'CustomerReference' => $order->id,
                'SourceInfo' => 'WhatsApp Order Flow'
            ];

            $mfObj = new MyFatoorahPayment($mfConfig);

            // paymentId = 0 means invoice URL (adjust if you want other payment methods)
            $payment = $mfObj->getInvoiceURL($payload, 0, $order->id);

            return $payment['invoiceURL'] ?? null;
        } catch (\Exception $e) {
            \Log::error('MyFatoorah invoice generation error: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * End session
     */
    private function endSession(string $to)
    {
        $this->stateService->resetState($to);
        return $this->whatsAppService->sendText($to, 'Thank you for contacting us. Goodbye!');
    }
}