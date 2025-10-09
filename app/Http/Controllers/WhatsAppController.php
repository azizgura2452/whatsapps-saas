<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Business;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppConversation;
use App\Services\WhatsApp\DynamicFlowService;

class WhatsAppController extends Controller
{
    protected $dynamicFlowService;

    public function __construct(DynamicFlowService $dynamicFlowService)
    {
        $this->dynamicFlowService = $dynamicFlowService;
    }

    /**
     * Handle verification requests from WhatsApp/Meta
     */
    public function verifyWebhook(Request $request)
    {
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');
        
        // Find business by verify token
        $business = Business::where('whatsapp_verify_token', $token)->first();
        
        if ($business) {
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

            $entry = $request->input('entry')[0] ?? [];
            $changes = $entry['changes'][0] ?? [];
            $value = $changes['value'] ?? [];
            
            // Extract business identifier from webhook
            $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
            $businessAccountId = $value['metadata']['business_account_id'] ?? null;

            if (!$phoneNumberId) {
                Log::warning('No phone_number_id in webhook');
                return response()->json(['status' => 'ignored'], 200);
            }

            // Find the business
            $business = Business::byWhatsAppPhoneId($phoneNumberId)
                ->where('is_active', true)
                ->first();

            if (!$business) {
                Log::warning("No active business found for phone_number_id: {$phoneNumberId}");
                return response()->json(['status' => 'business_not_found'], 200);
            }

            // Handle status updates
            if (isset($value['statuses'])) {
                $this->handleStatusUpdate($value['statuses'], $business);
                return response()->json(['status' => 'status_updated'], 200);
            }

            // Handle messages
            if (!isset($value['messages'])) {
                return response()->json(['status' => 'no_message'], 200);
            }

            $messageData = $this->extractMessageData($request);

            if (empty($messageData)) {
                return response()->json(['status' => 'no_message'], 200);
            }

            // Check for duplicate message
            if ($this->isDuplicateMessage($messageData['messageId'], $business)) {
                Log::info('Duplicate message detected: ' . $messageData['messageId']);
                return response()->json(['status' => 'duplicate_ignored'], 200);
            }

            // Store inbound message
            $this->storeInboundMessage($messageData, $business);

            // Process message through dynamic flow
            $this->dynamicFlowService->processMessage($messageData, $business);

            return response()->json(['status' => 'processed'], 200);

        } catch (\Throwable $e) {
            Log::error('Webhook processing error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['status' => 'error'], 200);
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
        $messageId = $messages['id'] ?? null;
        $timestamp = $messages['timestamp'] ?? time();
        $messageType = $messages['type'] ?? '';
        $messageText = $messages['text']['body'] ?? '';
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
     * Check if message is duplicate
     */
    private function isDuplicateMessage(string $messageId, Business $business): bool
    {
        if (empty($messageId)) {
            return false;
        }

        $cacheKey = "whatsapp_message_{$business->id}_{$messageId}";
        if (Cache::has($cacheKey)) {
            return true;
        }

        $exists = WhatsAppMessage::where('business_id', $business->id)
            ->where('whatsapp_message_id', $messageId)
            ->exists();

        if ($exists) {
            Cache::put($cacheKey, true, 86400);
            return true;
        }

        return false;
    }

    /**
     * Store inbound message
     */
    private function storeInboundMessage(array $messageData, Business $business): void
    {
        try {
            $conversation = $this->getOrCreateConversation(
                $messageData['from'], 
                $business
            );

            WhatsAppMessage::create([
                'business_id' => $business->id,
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

            $cacheKey = "whatsapp_message_{$business->id}_{$messageData['messageId']}";
            Cache::put($cacheKey, true, 86400);

        } catch (\Exception $e) {
            Log::error('Failed to store inbound message: ' . $e->getMessage());
        }
    }

    /**
     * Store outbound message
     */
    public function storeOutboundMessage(
        string $phoneNumber, 
        array $messageData, 
        Business $business,
        string $whatsappMessageId = null, 
        ?int $broadcastId = null, 
        string $status = 'sent'
    ): void {
        try {
            $conversation = $this->getOrCreateConversation($phoneNumber, $business);

            WhatsAppMessage::create([
                'business_id' => $business->id,
                'conversation_id' => $conversation->id,
                'broadcast_id' => $broadcastId,
                'whatsapp_message_id' => $whatsappMessageId,
                'phone_number' => $phoneNumber,
                'direction' => 'outbound',
                'message_type' => $messageData['type'] ?? 'text',
                'content' => $this->extractOutboundContent($messageData),
                'raw_data' => json_encode($messageData),
                'timestamp' => time(),
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to store outbound message: ' . $e->getMessage());
        }
    }

    /**
     * Get or create conversation
     */
    private function getOrCreateConversation(string $phoneNumber, Business $business): WhatsAppConversation
    {
        return WhatsAppConversation::firstOrCreate(
            [
                'business_id' => $business->id,
                'phone_number' => $phoneNumber
            ],
            [
                'status' => 'active',
                'last_message_at' => now(),
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
        }

        return 'Message received';
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
     * Handle status updates
     */
    private function handleStatusUpdate(array $statuses, Business $business): void
    {
        foreach ($statuses as $status) {
            $messageId = $status['id'] ?? null;
            $statusType = $status['status'] ?? null;

            if ($messageId && $statusType) {
                WhatsAppMessage::where('business_id', $business->id)
                    ->where('whatsapp_message_id', $messageId)
                    ->update(['status' => $statusType]);
            }
        }
    }
}