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
        if (!Schema::hasTable('goods_receiving_notes')) {
            Schema::create('goods_receiving_notes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_id')->nullable(); // Parent PO or Direct Purchase
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->string('grn_number')->unique();
                $table->date('grn_date');
                $table->string('status')->default('received'); // received, inspected, cancelled
                $table->boolean('is_invoiced')->default(0);
                $table->unsignedBigInteger('invoice_id')->nullable(); // Generated Purchase Bill/Invoice ID
                $table->text('remarks')->nullable();
                $table->string('carrier_info')->nullable(); // Driver, Truck/Bilty No
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('purchase_id');
                $table->index('vendor_id');
                $table->index('warehouse_id');
                $table->index('invoice_id');
            });
        }

        if (!Schema::hasTable('goods_receiving_note_items')) {
            Schema::create('goods_receiving_note_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('goods_receiving_note_id');
                $table->unsignedBigInteger('purchase_item_id')->nullable();
                $table->unsignedBigInteger('product_id');
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->decimal('received_qty', 12, 2)->default(0);
                $table->decimal('boxes', 12, 2)->default(0);
                $table->decimal('loose_pieces', 12, 2)->default(0);
                $table->longText('color')->nullable();
                $table->decimal('purchase_price', 12, 2)->default(0);
                $table->timestamps();

                $table->index('goods_receiving_note_id');
                $table->index('purchase_item_id');
                $table->index('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receiving_note_items');
        Schema::dropIfExists('goods_receiving_notes');
    }
};
