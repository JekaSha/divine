<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//docker exec -it backend php artisan migrate:refresh --path=database/migrations/2024_12_11_331611_create_packages_table.php
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {


        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->string('type', 64)->default("requests_per_month");
            $table->integer('days')->default(20)->nullable();
            $table->integer('requests')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 12);
            $table->json('stream')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
