<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Drop FK that uses the old unique index, then recreate it
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'product_id']);

            // New unique key: same product+size+color per user
            $table->unique(['user_id', 'product_id', 'size', 'color'], 'carts_user_product_size_color_unique');

            // Restore FK
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('carts_user_product_size_color_unique');
            $table->unique(['user_id', 'product_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
