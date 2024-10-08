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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Внешний ключ к таблице транзакций
            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->onDelete('cascade');

            // Статус заявки
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');

            // Внешний ключ к таблице пользователей
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Сумма валюты, которая должна быть отправлена
            $table->decimal('amount', 20, 8);

            // Адрес кошелька для отправки средств
            $table->string('wallet_address', 255);

            // Внешний ключ к таблице валют
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            // Внешний ключ к таблице протоколов
            $table->foreignId('protocol_id')
                ->constrained('currency_protocols')
                ->onDelete('cascade');

            // Текущий курс валюты на момент создания заявки
            $table->decimal('current_rate', 15, 8);

            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index('transaction_id');
            $table->index('user_id');
            $table->index('currency_id');
            $table->index('protocol_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
