<?php
// ============================================
// 1. Migration - Add scheduling fields to broadcasts
// database/migrations/2025_01_19_300000_add_scheduling_to_broadcasts.php
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed'])
                ->default('draft')
                ->after('broadcast_group_id');
            $table->timestamp('scheduled_at')->nullable()->after('status');
            $table->timestamp('sent_at')->nullable()->after('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropColumn(['status', 'scheduled_at', 'sent_at']);
        });
    }
};