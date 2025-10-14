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
            $table->decimal('price', 10, 2)->default(0)->after('duration_days');
            $table->foreignId('reseller_id')->nullable()->after('created_by')->constrained('resellers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_keys', function (Blueprint $table) {
            $table->dropForeign(['reseller_id']);
            $table->dropColumn(['price', 'reseller_id']);
        });
    }
};
