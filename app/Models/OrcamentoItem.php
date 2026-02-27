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
        'concluido_em',
        'id_user',
        'id_subcontratado',
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
        'concluido_em' => 'datetime',
    ];

    public function isConcluido(): bool
    {
        return $this->concluido_em !== null;
    }

    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class, 'id_orcamento');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function subcontratado(): BelongsTo
    {
        return $this->belongsTo(Subcontratado::class, 'id_subcontratado');
    }

    /**
     * Nome do técnico atribuído (user ou subcontratado do item).
     */
    public function getTecnicoNomeAttribute(): ?string
    {
        if ($this->id_user) {
            return $this->user?->name;
        }
        if ($this->id_subcontratado) {
            return $this->subcontratado?->nome;
        }

        return null;
    }
}
