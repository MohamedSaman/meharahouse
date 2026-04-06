<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipment_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique(); // e.g. SB-202504-001
            $table->string('name');                   // e.g. "April Batch 1"
            $table->enum('status', [
                'collecting',   // Accepting orders into this batch
                'purchased',    // Items purchased from Dubai vendor
                'packed',       // Items packed in Dubai
                'shipped',      // Dispatched from Dubai
                'in_transit',   // On the way to Sri Lanka
                'arrived',      // Arrived in Sri Lanka
                'distributing', // Waybills prepared, distributing locally
                'completed',    // All delivered
            ])->default('collecting');
            $table->string('courier_name')->nullable();    // International courier
            $table->string('tracking_number')->nullable(); // International tracking
            $table->decimal('courier_cost', 10, 2)->default(0);
            $table->date('expected_arrival')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_batches');
    }
};
