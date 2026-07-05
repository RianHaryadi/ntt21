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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('insurance_amount', 12, 2)->default(0);
            $table->decimal('total_price', 14, 2);
            $table->boolean('has_insurance')->default(false);

            $table->foreignId('promo_code_id')->nullable()->constrained('promotions')->nullOnDelete();

            $table->string('payment_method')->nullable();
            $table->string('payment_gateway_token')->nullable();
            $table->dateTime('payment_deadline')->nullable();
            $table->enum('status', ['pending', 'paid', 'confirmed', 'completed', 'cancelled', 'expired'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
