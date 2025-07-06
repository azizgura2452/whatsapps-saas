<?php
namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ConversationStateService
{
    // Define conversation flow states
    const STATE_INITIAL = 'initial';
    const STATE_LANGUAGE_SELECTED = 'language_selected';
    const STATE_MENU_SHOWN = 'menu_shown';
    const STATE_CATALOG_SHOWN = 'catalog_shown';
    const STATE_CATEGORY_SELECTED = 'category_selected';
    const STATE_PRODUCT_SELECTED = 'product_selected';
    const STATE_ORDER_STARTED = 'order_started';
    const STATE_NAME_REQUESTED = 'name_requested';
    const STATE_NAME_PROVIDED = 'name_provided';
    const STATE_ADDRESS_REQUESTED = 'address_requested';
    const STATE_ADDRESS_PROVIDED = 'address_provided';
    const STATE_PAYMENT_REQUESTED = 'payment_requested';
    const STATE_ORDER_COMPLETED = 'order_completed';
    const STATE_SUPPORT_STARTED = 'support_started';
    
    // Cache TTL in minutes (48 hours)
    const CACHE_TTL = 2880;
    
    /**
     * Get current state for a user
     */
    public function getCurrentState(string $phoneNumber): array
    {
        $stateData = Cache::get($this->getCacheKey($phoneNumber), [
            'state' => self::STATE_INITIAL,
            'data' => [],
            'language' => 'english',
            'last_activity' => now()->timestamp
        ]);
        
        Log::debug("Current state for $phoneNumber:", $stateData);
        return $stateData;
    }
    
    /**
     * Set state for a user
     */
    public function setState(string $phoneNumber, string $state, array $data = []): void
    {
        $currentState = $this->getCurrentState($phoneNumber);
        
        $stateData = [
            'state' => $state,
            'data' => array_merge($currentState['data'] ?? [], $data),
            'language' => $data['language'] ?? ($currentState['language'] ?? 'english'),
            'last_activity' => now()->timestamp
        ];
        
        Cache::put($this->getCacheKey($phoneNumber), $stateData, now()->addMinutes(self::CACHE_TTL));
        Log::debug("State updated for $phoneNumber:", $stateData);
    }
    
    /**
     * Add data to current state
     */
    public function addStateData(string $phoneNumber, array $data): void
    {
        $currentState = $this->getCurrentState($phoneNumber);
        $currentState['data'] = array_merge($currentState['data'] ?? [], $data);
        $currentState['last_activity'] = now()->timestamp;
        
        Cache::put($this->getCacheKey($phoneNumber), $currentState, now()->addMinutes(self::CACHE_TTL));
    }
    
    /**
     * Set language preference
     */
    public function setLanguage(string $phoneNumber, string $language): void
    {
        $currentState = $this->getCurrentState($phoneNumber);
        $currentState['language'] = $language;
        
        Cache::put($this->getCacheKey($phoneNumber), $currentState, now()->addMinutes(self::CACHE_TTL));
    }
    
    /**
     * Get language preference
     */
    public function getLanguage(string $phoneNumber): string
    {
        return $this->getCurrentState($phoneNumber)['language'] ?? 'english';
    }
    
    /**
     * Reset state for a user
     */
    public function resetState(string $phoneNumber): void
    {
        Cache::forget($this->getCacheKey($phoneNumber));
    }
    
    /**
     * Check if state is one of the given states
     */
    public function isInState(string $phoneNumber, array $states): bool
    {
        $currentState = $this->getCurrentState($phoneNumber)['state'];
        return in_array($currentState, $states);
    }
    
    /**
     * Get order data from state if available
     */
    public function getOrderData(string $phoneNumber): ?array
    {
        return $this->getCurrentState($phoneNumber)['data']['order'] ?? null;
    }
    
    /**
     * Get cache key for user
     */
    private function getCacheKey(string $phoneNumber): string
    {
        return "whatsapp_conversation_state:{$phoneNumber}";
    }
}