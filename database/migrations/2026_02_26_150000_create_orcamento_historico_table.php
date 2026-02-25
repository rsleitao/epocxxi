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
        Schema::create('orcamento_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_orcamento')->constrained('orcamentos')->cascadeOnDelete();
            $table->string('status_anterior')->nullable();
            $table->string('status_novo');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orcamento_historico');
    }
};
