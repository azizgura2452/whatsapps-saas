<?php
// ============================================
// database/migrations/2025_01_19_100000_create_broadcast_group_customers_table.php
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_group_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broadcast_group_id')->constrained('broadcast_groups')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['broadcast_group_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_group_customers');
    }
};