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
        Schema::table('processos', function (Blueprint $table) {
            $table->foreignId('id_requerente')->nullable()->after('id_orcamento')->constrained('requerentes')->nullOnDelete();
            $table->string('designacao')->nullable()->after('id_requerente');
            $table->foreignId('id_imovel')->nullable()->after('designacao')->constrained('imoveis')->nullOnDelete();
            // Numeração por ano: 25-0001, 25-0002, ... 26-0001 no ano seguinte
            $table->unsignedSmallInteger('ano')->nullable()->after('id_imovel');
            $table->unsignedInteger('numero_sequencial')->nullable()->after('ano');
            $table->unique(['ano', 'numero_sequencial']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropUnique(['ano', 'numero_sequencial']);
            $table->dropForeign(['id_requerente']);
            $table->dropForeign(['id_imovel']);
            $table->dropColumn(['id_requerente', 'designacao', 'id_imovel', 'ano', 'numero_sequencial']);
        });
    }
};
