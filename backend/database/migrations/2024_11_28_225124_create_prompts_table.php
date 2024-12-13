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
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->string('ai_model',32); // Модель ИИ (например, gpt4, gpt4o)
            $table->string('ai_type',32); // Тип ИИ (например, chatgpt)
            $table->text('template');
            $table->json('stream')->nullable(); // Дополнительные настройки
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
