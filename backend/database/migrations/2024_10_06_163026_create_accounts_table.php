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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('exchange_id');
            $table->string('name');
            $table->string('description');
            $table->string('api_key', 128);
            $table->string('api_secret', 128);
            $table->json('stream');

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();

            $table->foreign('exchange_id')->references('id')->on('exchanges')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
