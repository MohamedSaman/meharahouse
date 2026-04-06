<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            // Type: advance (first payment), balance (remaining), refund (if refunded)
            $table->enum('type', ['advance', 'balance', 'refund']);

            $table->decimal('amount', 10, 2);

            // Payment method used by customer
            $table->enum('method', ['bank_transfer', 'online', 'cash'])->default('bank_transfer');

            // Path to the uploaded receipt image (stored in public/payment-receipts/)
            $table->string('receipt_path')->nullable();

            // Bank reference number or online transaction ID
            $table->string('reference')->nullable();

            // Admin confirms or rejects the receipt
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');

            // Admin who reviewed and confirmed or rejected the payment
            $table->foreignId('confirmed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('confirmed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
