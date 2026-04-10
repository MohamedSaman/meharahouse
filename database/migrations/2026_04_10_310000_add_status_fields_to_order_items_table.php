<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // active | refunded | replaced | backordered
            $table->string('status')->default('active')->after('subtotal');
            // Amount refunded for this specific item (partial or full)
            $table->decimal('refund_amount', 10, 2)->nullable()->after('status');
            // Original ordered quantity before any stock decision changed it
            $table->unsignedInteger('original_qty')->nullable()->after('refund_amount');
            // Original subtotal before any stock decision changed it
            $table->decimal('original_ordered_subtotal', 10, 2)->nullable()->after('original_qty');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'refund_amount',
                'original_qty',
                'original_ordered_subtotal',
            ]);
        });
    }
};
