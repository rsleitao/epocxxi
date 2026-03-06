<?php

namespace App\Http\Controllers;

use App\Models\Gabinete;
use App\Models\Orcamento;
use App\Models\OrcamentoItem;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelatoriosController extends Controller
{
    public function index(Request $request): View
    {
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $idGabinete = $request->input('id_gabinete');
        $idUser = $request->input('id_user');

        $query = OrcamentoItem::query()
            ->where('estado', 'concluido')
            ->whereNotNull('concluido_em')
            ->with(['orcamento.gabinete', 'orcamento.imovel.tipoImovel', 'servico', 'user', 'subcontratado', 'tempoSegmentos']);

        if ($dataInicio) {
            $query->whereDate('concluido_em', '>=', $dataInicio);
        }
        if ($dataFim) {
            $query->whereDate('concluido_em', '<=', $dataFim);
        }
        if ($idGabinete) {
            $query->whereHas('orcamento', fn ($q) => $q->where('id_gabinete', $idGabinete));
        }
        if ($idUser) {
            $query->where('id_user', $idUser);
        }

        $itens = $query->get();
        $custoHora = (float) Setting::get('relatorios.custo_hora_medio', config('relatorios.custo_hora_medio', 25));

        $totalTrabalhos = $itens->count();
        $totalTempoSegundos = $itens->sum(fn ($i) => $i->total_tempo_segundos);
        $totalTempoFormatado = $this->formatarTempo($totalTempoSegundos);
        $totalFaturado = $itens->sum(fn ($i) => $this->precoCobradoItem($i));
        $totalCustoTempo = $itens->sum(fn ($i) => $this->custoTempoItem($i, $custoHora));
        $margem = $totalFaturado - $totalCustoTempo;
        $compensa = $totalCustoTempo <= 0 || $totalFaturado >= $totalCustoTempo;

        $porGabinete = $itens->groupBy(fn ($i) => $i->orcamento?->id_gabinete ?? 0)->map(function ($group, $idGab) use ($custoHora) {
            $primeiro = $group->first();
            $faturado = $group->sum(fn ($i) => $this->precoCobradoItem($i));
            $custo = $group->sum(fn ($i) => $this->custoTempoItem($i, $custoHora));
            return [
                'gabinete_nome' => $primeiro->orcamento?->gabinete?->nome ?? '—',
                'count' => $group->count(),
                'tempo_segundos' => $group->sum(fn ($i) => $i->total_tempo_segundos),
                'faturado' => $faturado,
                'custoTempo' => $custo,
                'margem' => $faturado - $custo,
                'compensa' => $custo <= 0 || $faturado >= $custo,
            ];
        })->values()->sortByDesc('count')->values()->all();

        foreach ($porGabinete as &$row) {
            $row['tempo_formatado'] = $this->formatarTempo($row['tempo_segundos']);
        }
        unset($row);

        $porTecnico = $itens->groupBy(function ($i) {
            if ($i->id_user) {
                return 'user-' . $i->id_user;
            }
            if ($i->id_subcontratado) {
                return 'sub-' . $i->id_subcontratado;
            }
            return 'sem';
        })->map(function ($group, $key) use ($custoHora) {
            $primeiro = $group->first();
            $nome = $primeiro->tecnico_nome ?? '—';
            if ($key === 'sem') {
                $nome = 'Sem técnico';
            }
            $faturado = $group->sum(fn ($i) => $this->precoCobradoItem($i));
            $custo = $group->sum(fn ($i) => $this->custoTempoItem($i, $custoHora));
            return [
                'tecnico_nome' => $nome,
                'count' => $group->count(),
                'tempo_segundos' => $group->sum(fn ($i) => $i->total_tempo_segundos),
                'faturado' => $faturado,
                'custoTempo' => $custo,
                'margem' => $faturado - $custo,
                'compensa' => $custo <= 0 || $faturado >= $custo,
            ];
        })->reject(fn ($_, $key) => $key === 'sem')->values()->sortByDesc('count')->values()->all();

        foreach ($porTecnico as &$row) {
            $row['tempo_formatado'] = $this->formatarTempo($row['tempo_segundos']);
        }
        unset($row);

        $porServico = $itens->groupBy(fn ($i) => $i->id_servico ?? 0)->map(function ($group, $idServico) use ($custoHora) {
            $primeiro = $group->first();
            $faturado = $group->sum(fn ($i) => $this->precoCobradoItem($i));
            $custo = $group->sum(fn ($i) => $this->custoTempoItem($i, $custoHora));
            return [
                'servico_nome' => $primeiro->servico?->nome ?? 'Serviço ocasional',
                'tipo_trabalho' => $primeiro->servico?->tipo_trabalho ?? '—',
                'count' => $group->count(),
                'tempo_segundos' => $group->sum(fn ($i) => $i->total_tempo_segundos),
                'faturado' => $faturado,
                'custoTempo' => $custo,
                'margem' => $faturado - $custo,
                'compensa' => $custo <= 0 || $faturado >= $custo,
            ];
        })->values()->sortByDesc('count')->values()->all();

        foreach ($porServico as &$row) {
            $row['tempo_formatado'] = $this->formatarTempo($row['tempo_segundos']);
        }
        unset($row);

        $porTipoImovel = $itens->groupBy(fn ($i) => $i->orcamento?->imovel?->id_tipo_imovel ?? 0)->map(function ($group, $idTipo) use ($custoHora) {
            $primeiro = $group->first();
            $nome = $primeiro->orcamento?->imovel?->tipoImovel?->tipo_imovel ?? 'Sem tipo';
            $faturado = $group->sum(fn ($i) => $this->precoCobradoItem($i));
            $custo = $group->sum(fn ($i) => $this->custoTempoItem($i, $custoHora));
            return [
                'tipo_imovel_nome' => $nome,
                'count' => $group->count(),
                'tempo_segundos' => $group->sum(fn ($i) => $i->total_tempo_segundos),
                'faturado' => $faturado,
                'custoTempo' => $custo,
                'margem' => $faturado - $custo,
                'compensa' => $custo <= 0 || $faturado >= $custo,
            ];
        })->values()->sortByDesc('faturado')->values()->all();

        foreach ($porTipoImovel as &$row) {
            $row['tempo_formatado'] = $this->formatarTempo($row['tempo_segundos']);
        }
        unset($row);

        $porMes = $itens->groupBy(fn ($i) => $i->concluido_em?->format('Y-m'))->map(function ($group, $anoMes) {
            $faturado = $group->sum(fn ($i) => $this->precoCobradoItem($i));
            return [
                'ano_mes' => $anoMes,
                'label' => \Carbon\Carbon::createFromFormat('Y-m', $anoMes)->format('m/Y'),
                'count' => $group->count(),
                'faturado' => $faturado,
                'tempo_segundos' => $group->sum(fn ($i) => $i->total_tempo_segundos),
            ];
        })->sortKeys()->values()->all();

        $totalPorFaturar = Orcamento::where('status', 'por_faturar')->with('itens')->get()->sum(function ($o) {
            return $o->itens->sum(fn ($i) => (float) $i->preco_base * (float) ($i->quantidade ?? 1));
        });

        $gabinetes = Gabinete::orderBy('nome')->get();
        $users = User::orderBy('name')->get();

        return view('gestao.relatorios', [
            'totalTrabalhos' => $totalTrabalhos,
            'totalTempoFormatado' => $totalTempoFormatado,
            'totalTempoSegundos' => $totalTempoSegundos,
            'totalFaturado' => $totalFaturado,
            'totalCustoTempo' => $totalCustoTempo,
            'totalPorFaturar' => $totalPorFaturar,
            'margem' => $margem,
            'compensa' => $compensa,
            'custoHora' => $custoHora,
            'porGabinete' => $porGabinete,
            'porTecnico' => $porTecnico,
            'porServico' => $porServico,
            'porTipoImovel' => $porTipoImovel,
            'porMes' => $porMes,
            'gabinetes' => $gabinetes,
            'users' => $users,
            'filtros' => [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'id_gabinete' => $idGabinete,
                'id_user' => $idUser,
            ],
        ]);
    }

    public function updateCustoHora(Request $request): RedirectResponse
    {
        $request->validate(['custo_hora' => 'required|numeric|min:0']);

        Setting::set('relatorios.custo_hora_medio', (float) $request->input('custo_hora'));

        return redirect()->back()->with('status', 'Custo horário atualizado.');
    }

    private function precoCobradoItem(OrcamentoItem $item): float
    {
        return (float) $item->preco_base * (float) ($item->quantidade ?? 1);
    }

    private function custoTempoItem(OrcamentoItem $item, float $custoHora): float
    {
        $horas = $item->total_tempo_segundos / 3600;

        return round($horas * $custoHora, 2);
    }

    private function formatarTempo(int $segundos): string
    {
        if ($segundos <= 0) {
            return '—';
        }
        $h = (int) floor($segundos / 3600);
        $m = (int) floor(($segundos % 3600) / 60);
        if ($h > 0) {
            return $h . ' h ' . $m . ' min';
        }
        return $m . ' min';
    }
}
