<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand enum to include BOTH old 'fulfilled' and new values so the update won't truncate
        DB::statement("ALTER TABLE `order_backorders` MODIFY `status` ENUM('pending','repurchasing','fulfilled','ready','dispatched','delivered','completed','cancelled') NOT NULL DEFAULT 'pending'");

        // Step 2: Migrate existing 'fulfilled' rows → 'completed'
        DB::table('order_backorders')->where('status', 'fulfilled')->update(['status' => 'completed']);

        // Step 3: Remove 'fulfilled' from the enum now that no rows use it
        DB::statement("ALTER TABLE `order_backorders` MODIFY `status` ENUM('pending','repurchasing','ready','dispatched','delivered','completed','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('order_backorders', function (Blueprint $table) {
            $table->string('backorder_number')->nullable()->after('id');
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('dispatched_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_backorders', function (Blueprint $table) {
            $table->dropColumn(['backorder_number', 'dispatched_at', 'delivered_at', 'dispatched_by']);
        });
        DB::statement("ALTER TABLE `order_backorders` MODIFY `status` ENUM('pending','repurchasing','fulfilled','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
