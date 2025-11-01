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
        Schema::create('product_set_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_set_id')->constrained('product_sets')->onDelete('cascade');
            $table->string('url');
            $table->bigInteger('shop_id');
            $table->bigInteger('item_id');
            $table->timestamps();
            
            // Prevent duplicate URLs in the same product set
            $table->unique(['product_set_id', 'url']);
            $table->index(['product_set_id', 'shop_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_set_items');
    }
};
