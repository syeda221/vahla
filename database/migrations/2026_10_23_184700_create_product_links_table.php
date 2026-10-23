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
        if (!Schema::hasTable('product_links')) {
            Schema::create('product_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('linked_product_id')->constrained('products')->onDelete('cascade');
                $table->string('variant_key', 100)->nullable();
                $table->string('variant_unit', 50)->nullable();
                $table->string('variant_name', 255)->nullable();
                $table->longText('variant_data')->nullable();
                $table->decimal('default_qty', 12, 2)->default(1.00);
                $table->timestamps();

                $table->index(['product_id', 'linked_product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_links');
    }
};
