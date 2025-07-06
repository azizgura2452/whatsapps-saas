<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Internal title for identification, e.g., "Order Shipped"
            $table->text('message'); // The actual message text, e.g., "Hi {{name}}, your order #{{order_id}} has shipped."
            $table->boolean('is_active')->default(true); // For enabling/disabling template
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};
