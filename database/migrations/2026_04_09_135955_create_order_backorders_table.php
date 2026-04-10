<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_backorders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('product_name'); // snapshot at time of backorder creation
            $table->unsignedInteger('ordered_qty');
            $table->unsignedInteger('available_qty');
            $table->unsignedInteger('short_qty');
            $table->enum('decision', ['repurchase', 'waitlist'])->nullable();
            $table->enum('status', ['pending', 'repurchasing', 'fulfilled', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_backorders');
    }
};
