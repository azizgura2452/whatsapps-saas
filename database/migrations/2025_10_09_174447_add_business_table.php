<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_phone_number_id')->unique();
            $table->string('whatsapp_business_account_id');
            $table->string('whatsapp_access_token');
            $table->string('whatsapp_catalog_id')->nullable();
            $table->string('whatsapp_verify_token')->nullable();
            $table->string('currency')->default('KWD');
            $table->decimal('delivery_charge', 10, 3)->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->index('whatsapp_phone_number_id');
            $table->index('whatsapp_business_account_id');
        });

        // Add business_id to existing tables
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index('business_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index('business_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index('business_id');
        });

        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index('business_id');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index('business_id');
        });

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index('business_id');
        });

        Schema::table('broadcast_groups', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->index('business_id');
        });
    }

    public function down()
    {
        Schema::table('broadcast_groups', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });

        Schema::dropIfExists('businesses');
    }
};