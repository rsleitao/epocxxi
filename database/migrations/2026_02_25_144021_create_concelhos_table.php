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
        Schema::create('concelhos', function (Blueprint $table) {
            $table->integer('id_concelho')->primary();
            $table->string('nome', 100);
            $table->integer('id_distrito');
            $table->boolean('ativo')->default(true);
            $table->foreign('id_distrito')->references('id_distrito')->on('distritos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concelhos');
    }
};
