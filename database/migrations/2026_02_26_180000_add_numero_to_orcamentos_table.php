<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Número do orçamento: YYNNNN (ex.: 250001, 260001). Ano em 2 dígitos + sequencial 4 dígitos por ano.
     */
    public function up(): void
    {
        Schema::table('orcamentos', function (Blueprint $table) {
            $table->string('numero', 10)->nullable()->unique()->after('id');
        });

        $this->backfillNumeros();
    }

    private function backfillNumeros(): void
    {
        $orcamentos = DB::table('orcamentos')->orderBy('created_at')->orderBy('id')->get();
        $porAno = [];
        foreach ($orcamentos as $o) {
            $ano = date('y', strtotime($o->created_at));
            if (! isset($porAno[$ano])) {
                $porAno[$ano] = 1;
            }
            $numero = $ano . str_pad((string) $porAno[$ano], 4, '0', STR_PAD_LEFT);
            $porAno[$ano]++;
            DB::table('orcamentos')->where('id', $o->id)->update(['numero' => $numero]);
        }
    }

    public function down(): void
    {
        Schema::table('orcamentos', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};
