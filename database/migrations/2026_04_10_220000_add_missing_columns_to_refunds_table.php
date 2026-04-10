<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // customer_id — link directly to the user who placed the order
            if (!Schema::hasColumn('refunds', 'customer_id')) {
                $table->foreignId('customer_id')
                      ->nullable()
                      ->after('order_id')
                      ->constrained('users')
                      ->nullOnDelete();
            }

            // 'cash' method was missing from the original enum — replace with string
            if (!Schema::hasColumn('refunds', 'status')) {
                $table->string('status', 20)->default('pending')->after('notes');
            }

            // Rename 'reference' → 'reference_number' for clarity
            // (only if old column exists and new one does not)
            if (Schema::hasColumn('refunds', 'reference') && !Schema::hasColumn('refunds', 'reference_number')) {
                $table->renameColumn('reference', 'reference_number');
            }

            // Proof of payment file path (screenshot / PDF)
            if (!Schema::hasColumn('refunds', 'proof_file')) {
                $table->string('proof_file')->nullable()->after('reference_number');
            }
        });

        // Widen method column to allow 'cash' in addition to existing values
        Schema::table('refunds', function (Blueprint $table) {
            $table->string('method', 30)->change();
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_id');

            if (Schema::hasColumn('refunds', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('refunds', 'reference_number') && !Schema::hasColumn('refunds', 'reference')) {
                $table->renameColumn('reference_number', 'reference');
            }

            if (Schema::hasColumn('refunds', 'proof_file')) {
                $table->dropColumn('proof_file');
            }
        });
    }
};
