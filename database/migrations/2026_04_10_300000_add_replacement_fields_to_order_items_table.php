<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_replaced')->default(false)->after('subtotal');
            $table->unsignedBigInteger('original_product_id')->nullable()->after('is_replaced');
            $table->string('original_product_name')->nullable()->after('original_product_id');
            $table->decimal('original_price', 10, 2)->nullable()->after('original_product_name');
            $table->decimal('original_subtotal', 10, 2)->nullable()->after('original_price');
            $table->text('replacement_notes')->nullable()->after('original_subtotal');
            $table->timestamp('replaced_at')->nullable()->after('replacement_notes');
            $table->unsignedBigInteger('replaced_by')->nullable()->after('replaced_at');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_replaced',
                'original_product_id',
                'original_product_name',
                'original_price',
                'original_subtotal',
                'replacement_notes',
                'replaced_at',
                'replaced_by',
            ]);
        });
    }
};
