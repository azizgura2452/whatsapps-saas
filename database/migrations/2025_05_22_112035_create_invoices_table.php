<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // primary key
            $table->unsignedBigInteger('order_id');
            $table->string('invoice_status');
            $table->string('invoice_reference')->nullable();
            $table->string('customer_reference')->nullable();
            $table->timestamp('created_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('expiry_time')->nullable();
            $table->decimal('invoice_value', 12, 3);
            $table->text('comments')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_mobile', 20)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('user_defined_field')->nullable();
            $table->string('invoice_display_value')->nullable();
            $table->decimal('due_deposit', 12, 3)->nullable();
            $table->string('deposit_status')->nullable();

            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
