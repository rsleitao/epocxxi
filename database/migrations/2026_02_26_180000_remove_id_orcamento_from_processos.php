<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Processo passa a ter vários orçamentos (via orcamentos.id_processo).
     * Remover id_orcamento de processos.
     */
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropForeign(['id_orcamento']);
            $table->dropColumn('id_orcamento');
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->foreignId('id_orcamento')->nullable()->after('id')->constrained('orcamentos')->nullOnDelete();
        });
    }
};
