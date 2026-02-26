<?php

namespace Database\Seeders;

use App\Models\DocumentoTipo;
use Illuminate\Database\Seeder;

class DocumentoTipoSeeder extends Seeder
{
    public function run(): void
    {
        if (DocumentoTipo::where('slug', 'orcamento')->exists()) {
            return;
        }

        DocumentoTipo::create([
            'nome' => 'Orçamento',
            'slug' => 'orcamento',
            'descricao' => 'Documento de orçamento para enviar ao cliente. Use os placeholders no Word (ex: ${designacao}, ${total}).',
        ]);
    }
}
