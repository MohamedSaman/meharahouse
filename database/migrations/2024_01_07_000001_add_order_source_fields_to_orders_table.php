<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add new columns only if they don't already exist (safe re-run)
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'source')) {
                $table->enum('source', ['website', 'whatsapp'])->default('website')->after('notes');
            }
            if (!Schema::hasColumn('orders', 'advance_percentage')) {
                $table->tinyInteger('advance_percentage')->unsigned()->default(50)->after('source');
            }
            if (!Schema::hasColumn('orders', 'advance_amount')) {
                $table->decimal('advance_amount', 10, 2)->default(0)->after('advance_percentage');
            }
            if (!Schema::hasColumn('orders', 'balance_amount')) {
                $table->decimal('balance_amount', 10, 2)->default(0)->after('advance_amount');
            }
            if (!Schema::hasColumn('orders', 'supplier_status')) {
                $table->enum('supplier_status', ['none', 'ordered', 'received', 'unavailable'])->default('none')->after('balance_amount');
            }
            if (!Schema::hasColumn('orders', 'refund_option')) {
                $table->enum('refund_option', ['refund', 'reorder'])->nullable()->after('supplier_status');
            }
        });

        // Step 2: First expand ENUM to include BOTH old and new values so the UPDATE doesn't truncate
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','processing','shipped','delivered','cancelled',
            'new','payment_received','confirmed','sourcing','dispatched','completed','refunded'
        ) NOT NULL DEFAULT 'pending'");

        // Step 3: Migrate existing status values to the new names
        DB::statement("UPDATE orders SET status = CASE
            WHEN status = 'pending'    THEN 'new'
            WHEN status = 'processing' THEN 'confirmed'
            WHEN status = 'shipped'    THEN 'dispatched'
            WHEN status = 'delivered'  THEN 'delivered'
            WHEN status = 'cancelled'  THEN 'cancelled'
            ELSE 'new'
        END");

        // Step 4: Now remove old values, keeping only the new enum set
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'new',
            'payment_received',
            'confirmed',
            'sourcing',
            'dispatched',
            'delivered',
            'completed',
            'refunded',
            'cancelled'
        ) NOT NULL DEFAULT 'new'");
    }

    public function down(): void
    {
        // Revert status values before dropping the column modification
        DB::statement("UPDATE orders SET status = CASE
            WHEN status = 'new'              THEN 'pending'
            WHEN status = 'payment_received' THEN 'pending'
            WHEN status = 'confirmed'        THEN 'processing'
            WHEN status = 'sourcing'         THEN 'processing'
            WHEN status = 'dispatched'       THEN 'shipped'
            WHEN status = 'delivered'        THEN 'delivered'
            WHEN status = 'completed'        THEN 'delivered'
            WHEN status = 'refunded'         THEN 'cancelled'
            WHEN status = 'cancelled'        THEN 'cancelled'
            ELSE 'pending'
        END");

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending', 'processing', 'shipped', 'delivered', 'cancelled'
        ) NOT NULL DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['source', 'advance_percentage', 'advance_amount', 'balance_amount', 'supplier_status', 'refund_option']);
        });
    }
};
