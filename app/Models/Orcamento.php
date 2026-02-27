<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orcamento extends Model
{
    protected $table = 'orcamentos';

    protected $fillable = [
        'status', 'numero', 'id_requerente', 'id_requerente_fatura', 'id_imovel', 'id_gabinete',
        'designacao', 'percentagem_iva', 'id_processo', 'data_convertido', 'data_faturado',
        'user_id', 'id_subcontratado',
    ];

    /**
     * Gera o próximo número de orçamento para o ano (formato YYNNNN, ex.: 250001).
     * Sequencial reinicia em 1 cada ano novo. Deve ser chamado dentro de uma transação.
     */
    public static function proximoNumeroParaAno(string $ano2digitos): string
    {
        $ultimo = static::where('numero', 'like', $ano2digitos . '%')
            ->whereRaw('LENGTH(numero) = 6')
            ->orderByDesc('numero')
            ->lockForUpdate()
            ->first();

        $seq = $ultimo ? (int) substr($ultimo->numero, 2) + 1 : 1;

        return $ano2digitos . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

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

    /**
     * Total do orçamento com IVA (soma das linhas: preco_base * quantidade + IVA por linha).
     */
    public function getTotalComIvaAttribute(): float
    {
        $ivaPctOrc = (float) ($this->percentagem_iva ?? 23);
        $total = 0.0;
        foreach ($this->itens as $i) {
            $valorLinha = (float) $i->preco_base * (float) ($i->quantidade ?? 1);
            $pctIva = (float) ($i->percentagem_iva ?? $this->percentagem_iva ?? 23);
            $total += $valorLinha + round($valorLinha * ($pctIva / 100), 2);
        }

        return round($total, 2);
    }

    /**
     * True se o orçamento está em execução e todos os itens têm concluido_em preenchido.
     */
    public function allItensConcluidos(): bool
    {
        if ($this->status !== 'em_execucao') {
            return false;
        }
        $total = $this->itens()->count();
        if ($total === 0) {
            return false;
        }

        return $this->itens()->whereNotNull('concluido_em')->count() === $total;
    }
}
