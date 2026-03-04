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
        Schema::table('requerentes', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('telefone');
        });

        Schema::table('gabinetes', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('telefone');
        });

        Schema::table('subcontratados', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('telefone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requerentes', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });

        Schema::table('gabinetes', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });

        Schema::table('subcontratados', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
    }
};

