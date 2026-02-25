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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nome')->nullable()->after('id');
            $table->string('cc')->nullable()->after('nome');
            $table->string('nif')->nullable()->after('cc');
            $table->string('dgeg')->nullable()->after('nif');
            $table->string('oet')->nullable()->after('dgeg');
            $table->string('oe')->nullable()->after('oet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nome', 'cc', 'nif', 'dgeg', 'oet', 'oe']);
        });
    }
};
