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

            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->decimal('amount', 20, 8);

            $table->string('wallet_address', 255);
            $table->string('email', 255);

            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            $table->foreignId('protocol_id')
                ->constrained('currency_protocols')
                ->onDelete('cascade');

            $table->decimal('current_rate', 15, 8);

            $table->json('stream')->nullable();

            $table->text('comment')->nullable();

            $table->string('hash', 64);

            $table->timestamps();

            $table->unique('hash');
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

