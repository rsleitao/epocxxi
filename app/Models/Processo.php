<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Processo extends Model
{
    protected $table = 'processos';

    protected $fillable = [
        'id_requerente',
        'designacao',
        'id_imovel',
        'ano',
        'numero_sequencial',
    ];

    protected $casts = [
        'ano' => 'integer',
        'numero_sequencial' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Processo $processo) {
            if ($processo->ano === null || $processo->numero_sequencial === null) {
                $ano = (int) date('Y');
                $processo->ano = $processo->ano ?? $ano;
                $processo->numero_sequencial = $processo->numero_sequencial
                    ?? self::proximoNumeroParaAno($processo->ano);
            }
        });
    }

    /**
     * Devolve o próximo número sequencial para o ano (com lock para evitar duplicados).
     */
    public static function proximoNumeroParaAno(int $ano): int
    {
        return (int) DB::transaction(function () use ($ano) {
            $max = self::where('ano', $ano)->lockForUpdate()->max('numero_sequencial');

            return ($max ?? 0) + 1;
        });
    }

    /**
     * Referência para exibição: 25-0001, 26-0002, etc.
     */
    public function getReferenciaAttribute(): ?string
    {
        if ($this->ano === null || $this->numero_sequencial === null) {
            return null;
        }

        return sprintf('%02d-%04d', $this->ano % 100, $this->numero_sequencial);
    }

    public function orcamentos(): HasMany
    {
        return $this->hasMany(Orcamento::class, 'id_processo')->orderByDesc('created_at');
    }

    public function requerente(): BelongsTo
    {
        return $this->belongsTo(Requerente::class, 'id_requerente');
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class, 'id_imovel');
    }
}
