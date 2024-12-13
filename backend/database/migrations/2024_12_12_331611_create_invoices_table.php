<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//docker exec -it backend php artisan migrate:refresh --path=database/migrations/2024_12_12_331611_create_invoices_table.php
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('merchant_id')->nullable()->index(); // Merchant association
            $table->decimal('total', 10, 2);
            $table->string('currency', 12);
            $table->json('packages')->nullable();
            $table->json('stream')->nullable();
            $table->json('response')->nullable();
            $table->string('hash')->unique();
            $table->enum('status', ['pending', 'paid', 'cancelled', 'declined', 'waiting'])->default('pending');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('merchant_id')->references('id')->on('merchants')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
