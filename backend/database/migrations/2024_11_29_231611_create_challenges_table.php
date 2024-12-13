<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('guest_hash');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_hash')->index();
            $table->foreignId('prompt_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('request');
            $table->text('prompt');
            $table->text('response')->nullable();
            $table->unsignedInteger('response_time')->nullable();
            $table->json('stream')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challenges');
    }
};
