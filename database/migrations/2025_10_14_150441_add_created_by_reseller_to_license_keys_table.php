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
        Schema::table('license_keys', function (Blueprint $table) {
            $table->foreignId('created_by_reseller_id')
                ->nullable()
                ->after('created_by')
                ->constrained('resellers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_keys', function (Blueprint $table) {
            $table->dropForeign(['created_by_reseller_id']);
            $table->dropColumn('created_by_reseller_id');
        });
    }
};
