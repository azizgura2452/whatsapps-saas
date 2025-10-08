<?php
// ============================================
// 1. Migration: Add broadcast_id to whatsapp_messages
// database/migrations/2025_01_19_200000_add_broadcast_id_to_whatsapp_messages.php
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->foreignId('broadcast_id')
                ->nullable()
                ->after('conversation_id')
                ->constrained('broadcasts')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropForeign(['broadcast_id']);
            $table->dropColumn('broadcast_id');
        });
    }
};