<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabalhoTempo extends Model
{
    protected $table = 'trabalho_tempos';

    protected $fillable = [
        'orcamento_item_id',
        'user_id',
        'subcontratado_id',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function orcamentoItem(): BelongsTo
    {
        return $this->belongsTo(OrcamentoItem::class, 'orcamento_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subcontratado(): BelongsTo
    {
        return $this->belongsTo(Subcontratado::class, 'subcontratado_id');
    }

    /** Duração do segmento em segundos (segmento aberto = até agora). */
    public function getDuracaoSegundosAttribute(): int
    {
        $inicio = $this->started_at instanceof Carbon
            ? $this->started_at
            : Carbon::parse($this->started_at, config('app.timezone'));
        $fim = $this->ended_at !== null
            ? ($this->ended_at instanceof Carbon ? $this->ended_at : Carbon::parse($this->ended_at, config('app.timezone')))
            : Carbon::now(config('app.timezone'));

        return (int) $inicio->diffInSeconds($fim, true);
    }
}
