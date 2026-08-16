<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('approver_level_1_id')
                ->after('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('approver_level_2_id')
                ->after('approver_level_1_id')
                ->constrained('users')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign([
                'approver_level_1_id',
            ]);

            $table->dropForeign([
                'approver_level_2_id',
            ]);

            $table->dropColumn([
                'approver_level_1_id',
                'approver_level_2_id',
            ]);
        });
    }
};
