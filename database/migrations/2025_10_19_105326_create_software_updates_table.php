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
        Schema::create('software_updates', function (Blueprint $table) {
            $table->id();
            $table->string('target')->index(); // e.g., 'live-kenceng'
            $table->string('version'); // e.g., '0.1.2'
            $table->text('notes')->nullable(); // Release notes
            $table->datetime('pub_date'); // Publication date
            $table->json('platforms'); // Platform-specific data
            $table->boolean('is_active')->default(true); // Whether this update is active
            $table->timestamps();
            
            // Ensure only one active version per target
            $table->unique(['target', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_updates');
    }
};
