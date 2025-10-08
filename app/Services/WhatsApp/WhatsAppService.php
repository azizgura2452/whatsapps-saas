<?php
namespace App\Services\WhatsApp;

use App\Http\Controllers\WhatsAppController;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $phoneId;
    protected $catalogId;
    protected $conversationState;
    protected $customerService;
    protected $wabaId;
    protected $graph_version;

    public function __construct(ConversationStateService $conversationState = null, CustomerService $customerService)
    {
        $this->token = config('services.whatsapp.access_token');
        $this->phoneId = config('services.whatsapp.phone_number_id');
        $this->catalogId = config('services.whatsapp.catalog_id');
        $this->conversationState = $conversationState;
        $this->customerService = $customerService;
        $this->wabaId = config('services.whatsapp.business_account_id');
        $this->graph_version = config('services.graph_version');
    }

    public function getAllCustomerNumbers(): array
    {
        return $this->customerService->getAllPhoneNumbers(); // assumes such a method exists
    }

    /**
     * Send a text message
     */
    public function sendText(string $to, string $message)
    {
        return $this->sendRequest([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ]);
    }

    public function sendMarketingTemplate(string $to, array $params, string $templateName = 'sale_offer', string $languageCode = 'en', ?int $broadcastId = null)
    {
        return $this->sendRequest([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'parameter_name' => 'offer_title',
                                'type' => 'text',
                                'text' => $params['offer_title']
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'parameter_name' => 'offer_description',
                                'type' => 'text',
                                'text' => $params['offer_description']
                            ],
                            [
                                'parameter_name' => 'coupon',
                                'type' => 'text',
                                'text' => $params['coupon']
                            ]
                        ]
                    ],
                    [
                        'type' => 'button',
                        'index' => 0,
                        'sub_type' => 'COPY_CODE',
                        'parameters' => [
                            [
                                'parameter_name' => 'coupon_code',
                                'type' => 'coupon_code',
                                'coupon_code' => $params['coupon']
                            ],
                        ]
                    ]
                ]
            ]
        ], $broadcastId);
    }


    /**
     * Send interactive buttons
     */
    public function sendButtons(string $to, string $bodyText, array $buttons, ?string $headerText = null)
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => ['text' => $bodyText],
                'action' => ['buttons' => $buttons]
            ]
        ];

        if ($headerText) {
            $payload['interactive']['header'] = [
                'type' => 'text',
                'text' => $headerText
            ];
        }

        return $this->sendRequest($payload);
    }


    /**
     * Send a list message
     */
    public function sendList(string $to, string $headerText, string $bodyText, array $sections)
    {
        return $this->sendRequest([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'header' => ['type' => 'text', 'text' => $headerText],
                'body' => ['text' => $bodyText],
                'action' => [
                    'button' => 'View Options',
                    'sections' => $sections
                ]
            ]
        ]);
    }

    /**
     * Send a single product
     */
    public function sendSingleProduct(string $to, string $productId)
    {
        return $this->sendRequest([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'product',
                'body' => [
                    'text' => 'Here is the product you requested:'
                ],
                'action' => [
                    'catalog_id' => $this->catalogId,
                    'product_retailer_id' => $productId
                ]
            ]
        ]);
    }

    /**
     * Send a product template message
     */
    public function sendProductTemplate(string $to, array $products, string $templateName = 'varsity_catalogue_en_prod', string $languageCode = 'en_US')
    {
        // Prepare product items (limit to 30 as per WhatsApp's limit)
        $productItems = array_map(function ($product) {
            return ['product_retailer_id' => $product['retailer_id']];
        }, array_slice($products, 0, 30));

        // Use the first product as thumbnail if available
        $thumbnailProductId = !empty($products) ? $products[0]['retailer_id'] : '';

        return $this->sendRequest([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ],
                'components' => [
                    [
                        'type' => 'button',
                        'sub_type' => 'mpm',
                        'index' => 0,
                        'parameters' => [
                            [
                                'type' => 'action',
                                'action' => [
                                    'thumbnail_product_retailer_id' => $thumbnailProductId,
                                    'sections' => [
                                        [
                                            'title' => 'Featured Products',
                                            'product_items' => $productItems
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Fetch catalog categories
     */
    public function fetchCatalogCategories()
    {
        $response = Http::withToken($this->token)
            ->withOptions(['verify' => false])
            ->get("https://graph.facebook.com/{$this->graph_version}/{$this->catalogId}/product_sets?fields=id,name");

        if (!$response->ok()) {
            Log::error('Failed to fetch catalog categories:', $response->json());
            return [];
        }

        return $response->json()['data'] ?? [];
    }

    /**
     * Fetch products from category
     */
    public function fetchProductsFromCategory(string $categoryId)
    {
        $response = Http::withToken($this->token)
            ->withOptions(['verify' => false])
            ->get("https://graph.facebook.com/{$this->graph_version}/$categoryId/products");

        if (!$response->ok()) {
            Log::error('Failed to fetch products from category:', $response->json());
            return [];
        }

        return $response->json()['data'] ?? [];
    }

    /**
     * Send HTTP request to WhatsApp API
     */
    protected function sendRequest(array $payload, ?int $broadcastId = null)
    {
        $customerObj = $this->customerService->findByWhatsapp($payload['to']);
        $customerId = $customerObj ? $customerObj->id : null;

        try {
            $response = Http::withToken($this->token)
                ->withOptions(['verify' => false])
                ->post("https://graph.facebook.com/{$this->graph_version}/{$this->phoneId}/messages", $payload);

            if (!$response->successful()) {
                $errorData = $response->json();
                Log::error('WhatsApp API error:', $errorData);

                // Store failed message
                app(WhatsAppController::class)->storeOutboundMessage(
                    $payload['to'],
                    $payload,
                    null,
                    $customerId,
                    $broadcastId,
                    'failed' // ADD STATUS PARAMETER
                );

                return response()->json(['status' => 'error', 'message' => 'Failed to send message'], 500);
            }

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['messages'][0]['id'])) {
                $messageId = $responseData['messages'][0]['id'];
                // Store outbound message in database
                app(WhatsAppController::class)->storeOutboundMessage($payload['to'], $payload, $messageId, $customerId, $broadcastId, 'sent');

                Log::info("WhatsApp message sent successfully to {$payload['to']}", [
                    'message_id' => $messageId,
                    'payload' => $payload
                ]);
            }

            return response()->json(['status' => 'sent', 'message_id' => $response->json()['messages'][0]['id'] ?? null]);
        } catch (\Exception $e) {
            Log::error('WhatsApp API exception: ' . $e->getMessage());

            // Store failed message on exception
            app(WhatsAppController::class)->storeOutboundMessage(
                $payload['to'],
                $payload,
                null,
                $customerId,
                $broadcastId,
                'failed' // ADD STATUS PARAMETER
            );

            return response()->json(['status' => 'error', 'message' => 'Exception occurred'], 500);
        }
    }

    /**
     * Get conversation history for a phone number
     */
    public function getConversationHistory(string $phoneNumber, int $limit = 50): array
    {
        return app(WhatsAppController::class)->getConversationHistory($phoneNumber, $limit);
    }

    /**
     * Check if we can send a message (to prevent spam)
     */
    public function canSendMessage(string $phoneNumber, string $content): bool
    {
        return app(WhatsAppController::class)->canSendMessage($phoneNumber, $content);
    }

    public function getTemplates()
    {
        try {
            $wabaId = $this->wabaId;
            $response = Http::withToken($this->token)
                ->withOptions(['verify' => false])
                ->get("https://graph.facebook.com/v22.0/{$wabaId}/message_templates");

            if (!$response->successful()) {
                Log::error('WhatsApp Template API error:', $response->json());
                return response()->json(['status' => 'error', 'message' => 'Failed to fetch templates'], 500);
            }

            $templates = $response->json('data') ?? [];

            Log::info('Fetched WhatsApp templates successfully.', [
                'count' => count($templates)
            ]);

            return $templates;
        } catch (\Exception $e) {
            Log::error('WhatsApp Template API exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Exception occurred'], 500);
        }
    }

    public function downloadMedia(string $mediaId): ?array
    {
        try {
            // Step 1: Metadata
            $metaResponse = Http::withToken($this->token)
                ->withOptions(['verify' => false])
                ->get("https://graph.facebook.com/v22.0/{$mediaId}");

            if (!$metaResponse->successful()) {
                Log::error("WhatsApp media metadata fetch failed", $metaResponse->json());
                return null;
            }

            $meta = $metaResponse->json();
            $url = $meta['url'] ?? null;
            $mime = $meta['mime_type'] ?? 'application/octet-stream';

            if (!$url) {
                return null;
            }

            // Step 2: Download
            $fileResponse = Http::withToken($this->token)
                ->withOptions(['verify' => false])
                ->get($url);

            if (!$fileResponse->successful()) {
                Log::error("WhatsApp media download failed", $fileResponse->json());
                return null;
            }

            return [
                'mime_type' => $mime,
                'content' => $fileResponse->body(),
            ];

        } catch (\Exception $e) {
            Log::error("WhatsApp media exception: " . $e->getMessage());
            return null;
        }
    }

}