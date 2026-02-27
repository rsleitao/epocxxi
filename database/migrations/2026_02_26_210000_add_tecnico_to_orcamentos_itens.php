<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orcamentos_itens', function (Blueprint $table) {
            $table->foreignId('id_user')->nullable()->after('concluido_em')->constrained('users')->nullOnDelete();
            $table->foreignId('id_subcontratado')->nullable()->after('id_user')->constrained('subcontratados')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orcamentos_itens', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_subcontratado']);
        });
    }
};
