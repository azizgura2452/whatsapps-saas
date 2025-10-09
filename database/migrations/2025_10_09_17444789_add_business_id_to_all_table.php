<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add business_id to existing tables
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('business_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('business_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('business_id');
            }
        });

        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_conversations', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('business_id');
            }
        });

        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_messages', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('business_id');
            }
        });

        Schema::table('broadcasts', function (Blueprint $table) {
            if (!Schema::hasColumn('broadcasts', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('business_id');
            }
        });

        Schema::table('broadcast_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('broadcast_groups', 'business_id')) {
                $table->foreignId('business_id')->nullable()->after('id')->constrained()->onDelete('cascade');
                $table->index('business_id');
            }
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
    }
};