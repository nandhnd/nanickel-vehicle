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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'approver'])
                ->default('admin')
                ->after('password');

            $table->string('jabatan')
              ->nullable()
              ->after('role');

            $table->unsignedTinyInteger('approval_level')
              ->nullable()
              ->after('jabatan');

            $table->foreignId('office_id')
                ->nullable()
                ->after('approval_level')
                ->constrained('offices')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->boolean('is_active')
              ->default(true)
              ->after('office_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            
            $table->dropColumn([
                'role',
                'jabatan',
                'approval_level',
                'office_id',
                'is_active'
            ]);
        });
    }
};
