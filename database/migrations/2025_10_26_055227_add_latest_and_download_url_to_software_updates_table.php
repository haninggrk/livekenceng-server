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
        Schema::table('software_updates', function (Blueprint $table) {
            $table->boolean('is_latest')->default(false)->after('is_active');
            $table->string('download_url')->nullable()->after('is_latest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('software_updates', function (Blueprint $table) {
            $table->dropColumn(['is_latest', 'download_url']);
        });
    }
};
