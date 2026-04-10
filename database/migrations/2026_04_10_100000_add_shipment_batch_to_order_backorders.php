<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_backorders', function (Blueprint $table) {
            $table->foreignId('shipment_batch_id')->nullable()->after('order_id')
                  ->constrained('shipment_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_backorders', function (Blueprint $table) {
            $table->dropForeign(['shipment_batch_id']);
            $table->dropColumn('shipment_batch_id');
        });
    }
};
