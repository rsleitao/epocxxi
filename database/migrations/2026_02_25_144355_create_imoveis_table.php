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
        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->nullable();
            $table->string('morada')->nullable();
            $table->integer('id_distrito')->nullable();
            $table->integer('id_concelho')->nullable();
            $table->integer('id_freguesia')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('coordenadas')->nullable();
            $table->string('localidade')->nullable();
            $table->foreignId('id_tipo_imovel')->nullable()->constrained('tipo_imoveis')->nullOnDelete();
            $table->timestamps();
            $table->foreign('id_distrito')->references('id_distrito')->on('distritos')->nullOnDelete();
            $table->foreign('id_concelho')->references('id_concelho')->on('concelhos')->nullOnDelete();
            $table->foreign('id_freguesia')->references('id_freguesia')->on('freguesias')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};
