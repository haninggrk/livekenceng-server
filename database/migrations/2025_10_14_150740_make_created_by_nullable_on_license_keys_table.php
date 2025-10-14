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
            // For SQLite, altering nullability directly may not be supported.
            // Easiest is to drop the foreign key constraint and re-add the column nullable if needed.
        });
        // Use raw SQL for portability in this context:
        if (Schema::hasColumn('license_keys', 'created_by')) {
            // SQLite cannot alter column easily; recreate temp table approach would be ideal, but for brevity use raw pragma off.
            // Here we will drop the constraint and recreate the column as nullable by table rebuild using raw SQL if using SQLite.
            if (config('database.default') === 'sqlite') {
                // Rebuild table with created_by nullable
                DB::statement('CREATE TABLE license_keys_new AS SELECT * FROM license_keys');
                Schema::drop('license_keys');
                Schema::create('license_keys', function (Blueprint $table) {
                    $table->id();
                    $table->string('code')->unique();
                    $table->integer('duration_days');
                    $table->boolean('is_used')->default(false);
                    $table->foreignId('used_by')->nullable()->constrained('members')->nullOnDelete();
                    $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
                    $table->foreignId('plan_id')->nullable()->constrained('license_plans')->nullOnDelete();
                    $table->decimal('price', 10, 2)->nullable();
                    $table->foreignId('reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
                    $table->foreignId('created_by_reseller_id')->nullable()->constrained('resellers')->nullOnDelete();
                    $table->timestamp('used_at')->nullable();
                    $table->timestamps();
                });
                // Copy back data; created_by becomes NULL where not present
                DB::statement('INSERT INTO license_keys (id, code, duration_days, is_used, used_by, created_by, plan_id, price, reseller_id, created_by_reseller_id, used_at, created_at, updated_at) SELECT id, code, duration_days, is_used, used_by, created_by, plan_id, price, reseller_id, created_by_reseller_id, used_at, created_at, updated_at FROM license_keys_new');
                DB::statement('DROP TABLE license_keys_new');
            } else {
                Schema::table('license_keys', function (Blueprint $table) {
                    $table->dropForeign(['created_by']);
                    $table->foreignId('created_by')->nullable()->change();
                    $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: reverting nullable back to NOT NULL would risk data loss if NULLs exist.
    }
};
