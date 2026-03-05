<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'estado',
        'nota_pendente',
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
        return $this->estado === 'concluido' || $this->concluido_em !== null;
    }

    /** Estados possíveis do item (trabalho). */
    public const ESTADO_EM_ESPERA = 'em_espera';
    public const ESTADO_EM_EXECUCAO = 'em_execucao';
    public const ESTADO_PENDENTE = 'pendente';
    public const ESTADO_CONCLUIDO = 'concluido';

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

    public function tempoSegmentos(): HasMany
    {
        return $this->hasMany(TrabalhoTempo::class, 'orcamento_item_id')->orderBy('started_at');
    }

    /**
     * Total de segundos contabilizados (segmentos fechados + segmento aberto até agora).
     */
    public function getTotalTempoSegundosAttribute(): int
    {
        $total = 0;
        foreach ($this->tempoSegmentos as $seg) {
            $total += $seg->duracao_segundos;
        }

        return $total;
    }

    /**
     * True se existe um segmento em aberto (cronómetro a correr).
     * Usa a relação já carregada quando existe, para refletir segmentos acabados de criar.
     */
    public function hasTempoAberto(): bool
    {
        if ($this->relationLoaded('tempoSegmentos')) {
            return $this->tempoSegmentos->contains(fn ($s) => $s->ended_at === null);
        }

        return $this->tempoSegmentos()->whereNull('ended_at')->exists();
    }

    /**
     * Unix timestamp do início do segmento em aberto (para cronómetro live no frontend). Null se não houver.
     */
    public function getTempoStartedAtAttribute(): ?int
    {
        $open = $this->tempoSegmentos->first(fn ($s) => $s->ended_at === null);

        return $open ? $open->started_at->timestamp : null;
    }

    /**
     * Fecha o segmento em aberto (pausa ou fim).
     */
    public function fecharTempoAberto(): void
    {
        $this->tempoSegmentos()->whereNull('ended_at')->update(['ended_at' => now()]);
    }

    /**
     * Abre um novo segmento de tempo (início do cronómetro). Requer técnico no item.
     */
    public function abrirSegmentoTempo(): void
    {
        if (! $this->id_user && ! $this->id_subcontratado) {
            return;
        }
        TrabalhoTempo::create([
            'orcamento_item_id' => $this->id,
            'user_id' => $this->id_user,
            'subcontratado_id' => $this->id_subcontratado,
            'started_at' => now(),
            'ended_at' => null,
        ]);
    }

    /**
     * Formata total de tempo em "X h Y min" ou "X min". Se existir pelo menos um segmento, nunca mostra "—" (mínimo "0 min").
     */
    public function getTempoTotalFormatadoAttribute(): string
    {
        $s = $this->total_tempo_segundos;
        if ($s < 0) {
            return '—';
        }
        $horas = (int) floor($s / 3600);
        $min = (int) floor(($s % 3600) / 60);
        if ($horas > 0) {
            return $horas . ' h ' . $min . ' min';
        }
        if ($min > 0) {
            return $min . ' min';
        }
        // 0 segundos: mostrar "0 min" se existir algum segmento (query direta para não depender da relação carregada)
        $temSegmentos = $this->tempoSegmentos()->exists();

        return $temSegmentos ? '0 min' : '—';
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
