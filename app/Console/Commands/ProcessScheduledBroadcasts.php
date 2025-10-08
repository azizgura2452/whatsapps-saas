<?php
namespace App\Console\Commands;

use App\Jobs\SendScheduledBroadcastJob;
use App\Models\Broadcast;
use Illuminate\Console\Command;

class ProcessScheduledBroadcasts extends Command
{
    protected $signature = 'broadcasts:process-scheduled';
    protected $description = 'Process scheduled broadcasts that are due to be sent';

    public function handle()
    {
        $this->info('Checking for scheduled broadcasts...');

        $broadcasts = Broadcast::due()->get();

        if ($broadcasts->isEmpty()) {
            $this->info('No broadcasts due for sending.');
            return 0;
        }

        $this->info("Found {$broadcasts->count()} broadcast(s) to process.");

        foreach ($broadcasts as $broadcast) {
            $this->info("Dispatching broadcast ID: {$broadcast->id}");
            
            SendScheduledBroadcastJob::dispatch($broadcast);
        }

        $this->info('All scheduled broadcasts have been queued.');
        return 0;
    }
}