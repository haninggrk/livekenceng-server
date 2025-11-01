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
        Schema::table('license_plans', function (Blueprint $table) {
            // Drop the unique constraint on duration_days first
            $table->dropUnique(['duration_days']);
            
            // Add app_id column
            $table->foreignId('app_id')->nullable()->after('duration_days')->constrained('apps')->nullOnDelete();
            
            // Add composite unique constraint for app_id + duration_days
            $table->unique(['app_id', 'duration_days']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_plans', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique(['app_id', 'duration_days']);
            
            // Drop foreign key and column
            $table->dropForeign(['app_id']);
            $table->dropColumn('app_id');
            
            // Restore the original unique constraint on duration_days
            $table->unique('duration_days');
        });
    }
};
