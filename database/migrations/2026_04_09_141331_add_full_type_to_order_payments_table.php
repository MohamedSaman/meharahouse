<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires re-declaring the full ENUM to add a new value
        DB::statement("ALTER TABLE `order_payments` MODIFY `type` ENUM('advance', 'balance', 'refund', 'full') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `order_payments` MODIFY `type` ENUM('advance', 'balance', 'refund') NOT NULL");
    }
};
