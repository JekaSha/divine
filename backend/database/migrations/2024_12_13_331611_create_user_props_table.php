<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//docker exec -it backend php artisan migrate:refresh --path=database/migrations/2024_12_13_331611_create_user_props_table.php
return new class extends Migration
{
    public function up()
    {
        Schema::create('user_props', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_props');
    }
};
