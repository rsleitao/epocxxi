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
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('rascunho');
            $table->foreignId('id_requerente')->nullable()->constrained('requerentes')->nullOnDelete();
            $table->foreignId('id_requerente_fatura')->nullable()->constrained('requerentes')->nullOnDelete();
            $table->foreignId('id_imovel')->nullable()->constrained('imoveis')->nullOnDelete();
            $table->foreignId('id_gabinete')->nullable()->constrained('gabinetes')->nullOnDelete();
            $table->string('designacao')->nullable();
            $table->unsignedBigInteger('id_processo')->nullable();
            $table->date('data_convertido')->nullable();
            $table->date('data_faturado')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_subcontratado')->nullable()->constrained('subcontratados')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamentos');
    }
};
