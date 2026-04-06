<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipment_batch_id')->nullable()->after('source')
                  ->constrained('shipment_batches')->nullOnDelete();
            $table->string('waybill_number')->nullable()->after('shipment_batch_id');
            $table->string('delivery_agent')->nullable()->after('waybill_number');
            $table->text('delivery_notes')->nullable()->after('delivery_agent');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipment_batch_id');
            $table->dropColumn(['waybill_number', 'delivery_agent', 'delivery_notes']);
        });
    }
};
