<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Renomear estado "convertido" para "em_execucao" nos orçamentos.
     * Novos estados (cancelado, por_faturar) passam a ser válidos; não alteramos a coluna status.
     */
    public function up(): void
    {
        DB::table('orcamentos')->where('status', 'convertido')->update(['status' => 'em_execucao']);
    }

    public function down(): void
    {
        DB::table('orcamentos')->where('status', 'em_execucao')->update(['status' => 'convertido']);
    }
};
