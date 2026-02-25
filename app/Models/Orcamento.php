<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orcamento extends Model
{
    protected $table = 'orcamentos';

    protected $fillable = [
        'status', 'id_requerente', 'id_requerente_fatura', 'id_imovel', 'id_gabinete',
        'designacao', 'percentagem_iva', 'id_processo', 'data_convertido', 'data_faturado',
        'user_id', 'id_subcontratado',
    ];

    protected $casts = [
        'percentagem_iva' => 'decimal:2',
        'data_convertido' => 'date',
        'data_faturado' => 'date',
    ];

    public function requerente(): BelongsTo
    {
        return $this->belongsTo(Requerente::class, 'id_requerente');
    }

    public function requerenteFatura(): BelongsTo
    {
        return $this->belongsTo(Requerente::class, 'id_requerente_fatura');
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class, 'id_imovel');
    }

    public function gabinete(): BelongsTo
    {
        return $this->belongsTo(Gabinete::class, 'id_gabinete');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'id_processo');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subcontratado(): BelongsTo
    {
        return $this->belongsTo(Subcontratado::class, 'id_subcontratado');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(OrcamentoItem::class, 'id_orcamento')->orderBy('id');
    }

    public function historico(): HasMany
    {
        return $this->hasMany(OrcamentoHistorico::class, 'id_orcamento')->orderByDesc('created_at');
    }
}
