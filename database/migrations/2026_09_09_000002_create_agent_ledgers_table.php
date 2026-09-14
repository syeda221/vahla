<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id');
            $table->date('date')->nullable();
            $table->string('reference')->nullable();
            $table->string('sale_invoice_no')->nullable();
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_ledgers');
    }
};