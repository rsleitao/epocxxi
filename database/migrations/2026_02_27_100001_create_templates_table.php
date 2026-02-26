<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_documento_tipo')->constrained('documento_tipos')->cascadeOnDelete();
            $table->string('nome');
            $table->string('ficheiro'); // path relativo em storage/app/templates/
            $table->string('nome_original')->nullable(); // nome do ficheiro ao fazer upload
            $table->string('mime_type', 100)->nullable(); // application/vnd.openxmlformats...
            $table->boolean('is_predefinido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
