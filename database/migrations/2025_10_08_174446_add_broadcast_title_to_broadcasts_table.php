<?php
// database/migrations/2025_01_19_xxxxxx_add_broadcast_title_to_broadcasts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->string('broadcast_title')->nullable()->after('whatsapp_template_name');
        });
    }

    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropColumn('broadcast_title');
        });
    }
};