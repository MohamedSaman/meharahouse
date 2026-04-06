<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_order_tokens', function (Blueprint $table) {
            $table->id();

            // Unique token string (64 hex chars = 32 random bytes) used in the public URL
            $table->string('token', 64)->unique();

            // Admin who generated this link
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // JSON array of {product_id, product_name, quantity, price} selected by admin
            $table->json('products');

            // Calculated totals at time of token creation
            $table->decimal('subtotal', 10, 2);
            $table->tinyInteger('advance_percentage')->unsigned()->default(50);
            $table->decimal('advance_amount', 10, 2);

            // Optional expiry — null means never expires until used
            $table->timestamp('expires_at')->nullable();

            // Set when the customer submits the form
            $table->timestamp('used_at')->nullable();

            // Set after customer submits — links token back to the created order
            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained('orders')
                  ->nullOnDelete();

            // Token lifecycle state
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');

            // Admin note, e.g. "for Ahmed — WhatsApp customer"
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_order_tokens');
    }
};
