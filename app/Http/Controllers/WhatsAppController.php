<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\WhatsApp\WhatsAppService;
use App\Services\WhatsApp\MessageHandlerService;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppConversation;

class WhatsAppController extends Controller
{
    protected $customerService;
    protected $orderService;
    protected $whatsAppService;
    protected $messageHandler;

    public function __construct(
        CustomerService $customerService,
        OrderService $orderService,
        WhatsAppService $whatsAppService,
        MessageHandlerService $messageHandler
    ) {
        $this->customerService = $customerService;
        $this->orderService = $orderService;
        $this->whatsAppService = $whatsAppService;
        $this->messageHandler = $messageHandler;
    }

    /**
     * Handle verification requests from WhatsApp/Meta
     */
    public function verifyWebhook(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');
        if ($token === $verifyToken) {
            // Log::info("Webhook verified:".$verifyToken);
            return response($challenge, 200);
        }

        return response('Verification failed', 403);
    }

    /**
     * Handle webhook events from WhatsApp/Meta
     */
    public function handleWebhook(Request $request)
    {
        try {
            Log::info('Webhook received: ' . json_encode(
                $request->all(),
                JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR
            ));

            $value = $request->input('entry')[0]['changes'][0]['value'] ?? [];

            if (isset($value['statuses'])) {
                Log::info('Status update received. Ignoring.');
                return response()->json(['status' => 'status_ignored'], 200);
            }

            if (!isset($value['messages'])) {
                Log::info('No message found. Ignoring.');
                return response()->json(['status' => 'ignored'], 200);
            }

            $messageData = $this->extractMessageData($request);

            if (empty($messageData)) {
                return response()->json(['status' => 'no_message'], 200);
            }

            // Check for duplicate message using WhatsApp message ID
            if ($this->isDuplicateMessage($messageData['messageId'])) {
                Log::info('Duplicate message detected: ' . $messageData['messageId']);
                return response()->json(['status' => 'duplicate_ignored'], 200);
            }

            session(['wa_user' => $messageData['from']]);

            // Create customer entry now that we have all info
            $customerObj =$this->customerService->getOrCreateCustomer(['whatsapp_number' => $messageData['from']]);
            $customerId = $customerObj->id;

            Log::info('Customer Data of : '.$messageData['from']. '----' . $customerObj);
            // Store inbound message
            $this->storeInboundMessage($messageData, $customerId);

            // Call routeMessage but don't return its response
            $this->routeMessage($messageData);

            return response()->json(['status' => 'processed'], 200);

        } catch (\Throwable $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 200); // Still 200 to Meta!
        }
    }


    /**
     * Extract message data from webhook request
     */
    private function extractMessageData(Request $request): array
    {
        $entry = $request->input('entry')[0] ?? [];
        $changes = $entry['changes'][0]['value'] ?? [];
        $messages = $changes['messages'][0] ?? [];

        if (empty($messages)) {
            return [];
        }

        $from = $messages['from'];
        $messageId = $messages['id'] ?? null; // WhatsApp message ID
        $timestamp = $messages['timestamp'] ?? time();
        $messageType = $messages['type'] ?? '';
        $messageText = isset($messages['text']['body']) ? $messages['text']['body'] : '';
        $buttonReplyId = $messages['interactive']['button_reply']['id'] ?? null;
        $listReplyId = $messages['interactive']['list_reply']['id'] ?? null;

        return [
            'messageId' => $messageId,
            'from' => $from,
            'timestamp' => $timestamp,
            'type' => $messageType,
            'text' => $messageText,
            'buttonReplyId' => $buttonReplyId,
            'listReplyId' => $listReplyId,
            'raw' => $messages
        ];
    }

    /**
     * Check if message is duplicate using cache and database
     */
    private function isDuplicateMessage(string $messageId): bool
    {
        if (empty($messageId)) {
            return false;
        }

        // Check cache first (faster)
        $cacheKey = "whatsapp_message_" . $messageId;
        if (Cache::has($cacheKey)) {
            return true;
        }

        // Check database
        $exists = WhatsAppMessage::where('whatsapp_message_id', $messageId)->exists();

        if ($exists) {
            // Store in cache for 24 hours
            Cache::put($cacheKey, true, 86400);
            return true;
        }

        return false;
    }

    /**
     * Store inbound message in database
     */
    private function storeInboundMessage(array $messageData, $customerId): void
    {
        try {
            // Get or create conversation
            $conversation = $this->getOrCreateConversation($messageData['from'], $customerId);

            // Store message
            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'whatsapp_message_id' => $messageData['messageId'],
                'phone_number' => $messageData['from'],
                'direction' => 'inbound',
                'message_type' => $messageData['type'],
                'content' => $this->extractMessageContent($messageData),
                'raw_data' => json_encode($messageData['raw']),
                'timestamp' => $messageData['timestamp'],
                'status' => 'received',
            ]);

            // Cache the message ID to prevent duplicates
            $cacheKey = "whatsapp_message_" . $messageData['messageId'];
            Cache::put($cacheKey, true, 86400); // 24 hours

            Log::info('Inbound message stored: ' . $messageData['messageId']);

        } catch (\Exception $e) {
            Log::error('Failed to store inbound message: ' . $e->getMessage());
        }
    }

    /**
     * Store outbound message in database
     */
    public function storeOutboundMessage(string $phoneNumber, array $messageData, string $whatsappMessageId = null, int $customerId = null): void
    {
        try {
            // Get or create conversation
            $conversation = $this->getOrCreateConversation($phoneNumber,$customerId);

            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'whatsapp_message_id' => $whatsappMessageId,
                'phone_number' => $phoneNumber,
                'direction' => 'outbound',
                'message_type' => $messageData['type'] ?? 'text',
                'content' => $this->extractOutboundContent($messageData),
                'raw_data' => json_encode($messageData),
                'timestamp' => time(),
                'status' => 'sent'
            ]);

            Log::info('Outbound message stored for: ' . $phoneNumber);

        } catch (\Exception $e) {
            Log::error('Failed to store outbound message: ' . $e->getMessage());
        }
    }

    /**
     * Get or create conversation for a phone number
     */
    private function getOrCreateConversation(string $phoneNumber, int $customerId): WhatsAppConversation
    {
        return WhatsAppConversation::firstOrCreate(
            ['phone_number' => $phoneNumber],
            [
                'phone_number' => $phoneNumber,
                'status' => 'active',
                'last_message_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'customer_id' => $customerId,
            ]
        );
    }

    /**
     * Extract content from inbound message
     */
    private function extractMessageContent(array $messageData): ?string
    {
        switch ($messageData['type']) {
            case 'text':
                return $messageData['text'];
            case 'interactive':
                if ($messageData['buttonReplyId']) {
                    return 'Button: ' . $messageData['buttonReplyId'];
                }
                if ($messageData['listReplyId']) {
                    return 'List: ' . $messageData['listReplyId'];
                }
                break;
            case 'order':
                return 'Order placed';
            case 'image':
            case 'video':
            case 'audio':
            case 'document':
                return ucfirst($messageData['type']) . ' received';
            default:
                return 'Message received';
        }

        return null;
    }

    /**
     * Extract content from outbound message
     */
    private function extractOutboundContent(array $messageData): ?string
    {
        if (isset($messageData['text']['body'])) {
            return $messageData['text']['body'];
        }

        if (isset($messageData['interactive']['body']['text'])) {
            return $messageData['interactive']['body']['text'];
        }

        if (isset($messageData['template']['name'])) {
            return 'Template: ' . $messageData['template']['name'];
        }

        return 'Message sent';
    }

    /**
     * Handle status updates for outbound messages
     */
    private function handleStatusUpdate(array $statuses): void
    {
        foreach ($statuses as $status) {
            $messageId = $status['id'] ?? null;
            $statusType = $status['status'] ?? null;

            if ($messageId && $statusType) {
                WhatsAppMessage::where('whatsapp_message_id', $messageId)
                    ->update(['status' => $statusType]);

                Log::info("Message status updated: {$messageId} -> {$statusType}");
            }
        }
    }

    /**
     * Check if similar message was recently sent to avoid spam
     */
    public function canSendMessage(string $phoneNumber, string $messageContent): bool
    {
        $recentMessage = WhatsAppMessage::where('phone_number', $phoneNumber)
            ->where('direction', 'outbound')
            ->where('content', $messageContent)
            ->where('created_at', '>=', now()->subMinutes(5)) // Within last 5 minutes
            ->first();

        return !$recentMessage;
    }

    /**
     * Get conversation history for a phone number
     */
    public function getConversationHistory(string $phoneNumber, int $limit = 50): array
    {
        $conversation = WhatsAppConversation::where('phone_number', $phoneNumber)->first();

        if (!$conversation) {
            return [];
        }

        $messages = WhatsAppMessage::where('conversation_id', $conversation->id)
            ->orderBy('timestamp', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values()
            ->toArray();

        return $messages;
    }

    /**
     * Route message to appropriate handler
     */
    private function routeMessage(array $messageData)
    {
        // Handle order messages
        if ($messageData['type'] === 'order') {
            return $this->messageHandler->handleOrderMessage(
                $messageData['from'],
                $messageData['raw']['order'],
                $this->customerService,
                $this->orderService
            );
        }

        // Handle button replies
        if ($messageData['buttonReplyId']) {
            return $this->messageHandler->handleButtonReply(
                $messageData['from'],
                $messageData['buttonReplyId']
            );
        }

        // Handle list replies
        if ($messageData['listReplyId']) {
            return $this->messageHandler->handleListReply(
                $messageData['from'],
                $messageData['listReplyId']
            );
        }

        // Handle text messages
        if (!empty($messageData['text'])) {
            return $this->messageHandler->handleTextMessage(
                $messageData['from'],
                $messageData['text']
            );
        }

        return response()->json(['status' => 'ignored']);
    }
}