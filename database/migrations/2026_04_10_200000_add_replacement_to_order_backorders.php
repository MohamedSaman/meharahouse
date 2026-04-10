<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: expand decision ENUM to include 'replace'
        DB::statement("ALTER TABLE `order_backorders` MODIFY `decision` ENUM('repurchase','waitlist','replace') NULL");

        // Step 2: add replacement columns
        Schema::table('order_backorders', function (Blueprint $table) {
            $table->foreignId('replacement_product_id')
                  ->nullable()
                  ->after('product_id')
                  ->constrained('products')
                  ->nullOnDelete();
            $table->decimal('replacement_price', 10, 2)->nullable()->after('replacement_product_id');
            $table->text('replacement_notes')->nullable()->after('replacement_price');
        });
    }

    public function down(): void
    {
        Schema::table('order_backorders', function (Blueprint $table) {
            $table->dropForeign(['replacement_product_id']);
            $table->dropColumn(['replacement_product_id', 'replacement_price', 'replacement_notes']);
        });
        DB::statement("ALTER TABLE `order_backorders` MODIFY `decision` ENUM('repurchase','waitlist') NULL");
    }
};
