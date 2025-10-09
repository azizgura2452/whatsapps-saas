<?php

namespace App\Services\WhatsApp;

use App\Models\Business;
use App\Http\Controllers\WhatsAppController;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $conversationState;
    protected $customerService;
    protected $graph_version;

    public function __construct(ConversationStateService $conversationState, CustomerService $customerService)
    {
        $this->conversationState = $conversationState;
        $this->customerService = $customerService;
        $this->graph_version = config('services.graph_version', 'v18.0');
    }

    /**
     * Send a text message
     */
    public function sendText(string $to, Business $business, string $message)
    {
        return $this->sendRequest([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ], $business);
    }

    /**
     * Send interactive buttons
     */
    public function sendButtons(string $to, Business $business, string $bodyText, array $buttons, ?string $headerText = null)
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

        return $this->sendRequest($payload, $business);
    }

    /**
     * Send a list message
     */
    public function sendList(string $to, Business $business, string $bodyText, array $sections, ?string $headerText = null)
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $bodyText],
                'action' => [
                    'button' => 'View Options',
                    'sections' => $sections
                ]
            ]
        ];

        if ($headerText) {
            $payload['interactive']['header'] = [
                'type' => 'text',
                'text' => $headerText
            ];
        }

        return $this->sendRequest($payload, $business);
    }

    /**
     * Send a single product
     */
    public function sendSingleProduct(string $to, Business $business, string $productId)
    {
        if (!$business->whatsapp_catalog_id) {
            return $this->sendText($to, $business, 'Product catalog not configured.');
        }

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
                    'catalog_id' => $business->whatsapp_catalog_id,
                    'product_retailer_id' => $productId
                ]
            ]
        ], $business);
    }

    /**
     * Send catalog categories
     */
    public function sendCatalogCategories(string $to, Business $business)
    {
        $categories = $this->fetchCatalogCategories($business);

        if (empty($categories)) {
            return $this->sendText($to, $business, 'Unable to load catalog categories.');
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

        return $this->sendList(
            $to,
            $business,
            'Choose one of the categories below',
            $sections,
            'Product Categories'
        );
    }

    /**
     * Send products from category
     */
    public function sendProductsFromCategory(string $to, Business $business, string $categoryId)
    {
        $products = $this->fetchProductsFromCategory($business, $categoryId);

        if (empty($products)) {
            return $this->sendText($to, $business, 'No products found in this category.');
        }

        return $this->sendProductTemplate($to, $business, $products);
    }

    /**
     * Send a product template message
     */
    public function sendProductTemplate(string $to, Business $business, array $products, string $templateName = 'varsity_catalogue_en_prod', string $languageCode = 'en_US')
    {
        $productItems = array_map(function ($product) {
            return ['product_retailer_id' => $product['retailer_id']];
        }, array_slice($products, 0, 30));

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
        ], $business);
    }

    /**
     * Send marketing template
     */
    public function sendMarketingTemplate(
        string $to, 
        Business $business,
        array $params, 
        string $templateName = 'sale_offer', 
        string $languageCode = 'en',
        ?int $broadcastId = null
    ) {
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
        ], $business, $broadcastId);
    }

    /**
     * Fetch catalog categories
     */
    public function fetchCatalogCategories(Business $business)
    {
        if (!$business->whatsapp_catalog_id) {
            return [];
        }

        $response = Http::withToken($business->whatsapp_access_token)
            ->withOptions(['verify' => false])
            ->get("https://graph.facebook.com/{$this->graph_version}/{$business->whatsapp_catalog_id}/product_sets?fields=id,name");

        if (!$response->ok()) {
            Log::error('Failed to fetch catalog categories:', $response->json());
            return [];
        }

        return $response->json()['data'] ?? [];
    }

    /**
     * Fetch products from category
     */
    public function fetchProductsFromCategory(Business $business, string $categoryId)
    {
        $response = Http::withToken($business->whatsapp_access_token)
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
    protected function sendRequest(array $payload, Business $business, ?int $broadcastId = null)
    {
        $customerObj = $this->customerService->findByWhatsapp($payload['to'], $business->id);
        $customerId = $customerObj ? $customerObj->id : null;

        try {
            $response = Http::withToken($business->whatsapp_access_token)
                ->withOptions(['verify' => false])
                ->post("https://graph.facebook.com/{$this->graph_version}/{$business->whatsapp_phone_number_id}/messages", $payload);

            if (!$response->successful()) {
                $errorData = $response->json();
                Log::error('WhatsApp API error:', $errorData);

                app(WhatsAppController::class)->storeOutboundMessage(
                    $payload['to'],
                    $payload,
                    $business,
                    null,
                    $broadcastId,
                    'failed'
                );

                return response()->json(['status' => 'error', 'message' => 'Failed to send message'], 500);
            }

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['messages'][0]['id'])) {
                $messageId = $responseData['messages'][0]['id'];
                
                app(WhatsAppController::class)->storeOutboundMessage(
                    $payload['to'], 
                    $payload, 
                    $business,
                    $messageId, 
                    $broadcastId, 
                    'sent'
                );

                Log::info("WhatsApp message sent successfully to {$payload['to']}", [
                    'message_id' => $messageId,
                    'business_id' => $business->id
                ]);
            }

            return response()->json([
                'status' => 'sent', 
                'message_id' => $responseData['messages'][0]['id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('WhatsApp API exception: ' . $e->getMessage());

            app(WhatsAppController::class)->storeOutboundMessage(
                $payload['to'],
                $payload,
                $business,
                null,
                $broadcastId,
                'failed'
            );

            return response()->json(['status' => 'error', 'message' => 'Exception occurred'], 500);
        }
    }

    /**
     * Get templates for business
     */
    public function getTemplates(Business $business)
    {
        try {
            $response = Http::withToken($business->whatsapp_access_token)
                ->withOptions(['verify' => false])
                ->get("https://graph.facebook.com/{$this->graph_version}/{$business->whatsapp_business_account_id}/message_templates");

            if (!$response->successful()) {
                Log::error('WhatsApp Template API error:', $response->json());
                return [];
            }

            return $response->json('data') ?? [];
            
        } catch (\Exception $e) {
            Log::error('WhatsApp Template API exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Download media
     */
    public function downloadMedia(string $mediaId, Business $business): ?array
    {
        try {
            $metaResponse = Http::withToken($business->whatsapp_access_token)
                ->withOptions(['verify' => false])
                ->get("https://graph.facebook.com/{$this->graph_version}/{$mediaId}");

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

            $fileResponse = Http::withToken($business->whatsapp_access_token)
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