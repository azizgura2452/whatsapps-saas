<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->enum('status', ['active', 'closed', 'archived'])->default('active');
            $table->timestamp('last_message_at')->nullable();
            $table->json('metadata')->nullable(); // Store additional conversation data
            $table->timestamps();

            $table->index(['phone_number', 'status']);
            $table->index('last_message_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
