<?php
namespace App\Jobs;

use App\Models\Broadcast;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendScheduledBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout
    public $tries = 1;

    public function __construct(
        public Broadcast $broadcast
    ) {
    }

    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            Log::info("Starting broadcast job for ID: {$this->broadcast->id}");

            // Update status to sending
            $this->broadcast->update(['status' => 'sending']);

            // Parse template parameters
            $parts = explode('~', $this->broadcast->custom_template);

            if (count($parts) < 3 || in_array('', $parts, true)) {
                throw new \Exception("Missing template parameters for broadcast ID {$this->broadcast->id}");
            }

            $params = [
                'offer_title' => $parts[0] ?? '',
                'offer_description' => $parts[1] ?? '',
                'coupon' => $parts[2] ?? ''
            ];

            // Get recipients
            $recipients = $this->getRecipients();

            $successCount = 0;
            $failCount = 0;

            foreach ($recipients as $number) {
                try {
                    $whatsAppService->sendMarketingTemplate(
                        $number,
                        $params,
                        $this->broadcast->whatsapp_template_name,
                        'en',
                        $this->broadcast->id
                    );
                    $successCount++;
                    sleep(5); // Rate limiting
                } catch (\Throwable $e) {
                    $failCount++;
                    Log::error("Failed to send to {$number}: {$e->getMessage()}");
                }
            }

            // Update broadcast status
            $this->broadcast->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("Broadcast {$this->broadcast->id} completed. Success: {$successCount}, Failed: {$failCount}");

        } catch (\Exception $e) {
            Log::error("Broadcast job failed for ID {$this->broadcast->id}: {$e->getMessage()}");
            
            $this->broadcast->update(['status' => 'failed']);
            
            throw $e;
        }
    }

    private function getRecipients(): array
    {
        if ($this->broadcast->custom_recipients) {
            return array_map('trim', explode(',', $this->broadcast->custom_recipients));
        }

        if ($this->broadcast->broadcast_group_id) {
            return $this->broadcast->broadcastGroup->getCustomerPhoneNumbers();
        }

        // All customers
        return app(WhatsAppService::class)->getAllCustomerNumbers();
    }
}