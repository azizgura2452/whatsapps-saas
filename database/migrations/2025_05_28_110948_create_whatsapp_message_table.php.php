<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->onDelete('cascade');
            $table->string('whatsapp_message_id')->nullable()->unique(); // WhatsApp's message ID
            $table->string('phone_number');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('message_type')->default('text'); // text, image, interactive, etc.
            $table->text('content')->nullable();
            $table->json('raw_data')->nullable(); // Store complete webhook data
            $table->bigInteger('timestamp'); // WhatsApp timestamp
            $table->enum('status', ['sent', 'delivered', 'read', 'failed', 'received'])->default('sent');
            $table->timestamps();

            $table->index(['conversation_id', 'timestamp']);
            $table->index(['phone_number', 'direction']);
            $table->index('whatsapp_message_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
