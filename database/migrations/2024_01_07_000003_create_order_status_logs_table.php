<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            // The status the order was in before this change (null for initial creation)
            $table->string('from_status')->nullable();

            // The status the order moved into
            $table->string('to_status');

            // Optional admin note explaining the status change
            $table->text('notes')->nullable();

            // The admin or staff member who triggered this change (null = system/customer)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
