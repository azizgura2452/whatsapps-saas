<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_template_id')->constrained();
            $table->text('custom_template');
            $table->text('custom_recipients')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('broadcasts');
    }
};