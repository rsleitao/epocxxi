<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrcamentoItem extends Model
{
    protected $table = 'orcamentos_itens';

    protected $fillable = [
        'id_orcamento',
        'id_servico',
        'descricao',
        'preco_base',
        'quantidade',
        'prazo_data',
        'percentagem_iva',
    ];

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class, 'id_servico');
    }

    protected $casts = [
        'preco_base' => 'decimal:2',
        'quantidade' => 'decimal:2',
        'prazo_data' => 'date',
        'percentagem_iva' => 'decimal:2',
    ];

    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class, 'id_orcamento');
    }
}
