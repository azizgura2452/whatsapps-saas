<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_flow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('step_type');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'order']);
        });

        Schema::create('chat_flow_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_flow_step_id')->constrained()->onDelete('cascade');
            $table->string('language')->default('english');
            $table->string('message_type');
            $table->text('message_content');
            $table->json('buttons')->nullable();
            $table->json('list_sections')->nullable();
            $table->string('template_name')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_flow_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_flow_step_id')->constrained()->onDelete('cascade');
            $table->string('trigger_type');
            $table->text('trigger_value');
            $table->foreignId('next_step_id')->nullable()->constrained('chat_flow_steps')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_flow_triggers');
        Schema::dropIfExists('chat_flow_messages');
        Schema::dropIfExists('chat_flow_steps');
    }
};