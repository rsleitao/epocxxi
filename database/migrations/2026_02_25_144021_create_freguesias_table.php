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
        Schema::create('freguesias', function (Blueprint $table) {
            $table->integer('id_freguesia')->primary();
            $table->string('nome', 150);
            $table->integer('id_concelho');
            $table->boolean('ativo')->default(true);
            $table->foreign('id_concelho')->references('id_concelho')->on('concelhos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freguesias');
    }
};
