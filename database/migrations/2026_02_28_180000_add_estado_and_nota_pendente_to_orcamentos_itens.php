<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orcamentos_itens', function (Blueprint $table) {
            $table->string('estado', 20)->default('em_espera')->after('id_subcontratado');
            $table->text('nota_pendente')->nullable()->after('estado');
        });

        // Existentes: concluido_em preenchido -> estado concluido; senão em_espera
        \DB::table('orcamentos_itens')
            ->whereNotNull('concluido_em')
            ->update(['estado' => 'concluido']);
    }

    public function down(): void
    {
        Schema::table('orcamentos_itens', function (Blueprint $table) {
            $table->dropColumn(['estado', 'nota_pendente']);
        });
    }
};
