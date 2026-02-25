<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrcamentoHistorico extends Model
{
    protected $table = 'orcamento_historico';

    protected $fillable = [
        'id_orcamento',
        'status_anterior',
        'status_novo',
        'user_id',
    ];

    public function orcamento(): BelongsTo
    {
        return $this->belongsTo(Orcamento::class, 'id_orcamento');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
