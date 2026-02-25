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
        Schema::table('servicos', function (Blueprint $table) {
            $table->string('tipo_trabalho')->nullable()->after('ativo');
        });

        Schema::table('orcamentos_itens', function (Blueprint $table) {
            $table->foreignId('id_servico')->nullable()->after('id_orcamento')->constrained('servicos')->nullOnDelete();
            $table->dropColumn('tipo_trabalho');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orcamentos_itens', function (Blueprint $table) {
            $table->dropForeign(['id_servico']);
            $table->string('tipo_trabalho')->nullable()->after('prazo_data');
        });

        Schema::table('servicos', function (Blueprint $table) {
            $table->dropColumn('tipo_trabalho');
        });
    }
};
