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
        Schema::create('delivery_challan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_challan_id');
            $table->unsignedBigInteger('sale_item_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->decimal('delivered_qty', 12, 2); // representing total_pieces
            $table->decimal('boxes', 12, 2)->default(0);
            $table->decimal('loose_pieces', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_challan_items');
    }
};
