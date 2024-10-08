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
        Schema::create('currency_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->string('description');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_protocols');
    }
};
