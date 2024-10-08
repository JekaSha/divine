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
        Schema::create('currency_exchange', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_currency_id');
            $table->unsignedBigInteger('to_currency_id');
            $table->unsignedBigInteger('exchange_id');
            $table->decimal('current_rate', 15, 8);
            $table->timestamps();

            $table->foreign('from_currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('to_currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('exchange_id')->references('id')->on('exchanges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_exchange');
    }
};
