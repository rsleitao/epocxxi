<?php

namespace App\Http\Controllers;

use App\Models\Gabinete;
use App\Models\OrcamentoItem;
use App\Models\User;
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
            ->with(['orcamento.gabinete', 'servico', 'user', 'tempoSegmentos']);

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

        $totalTrabalhos = $itens->count();
        $totalTempoSegundos = $itens->sum(fn ($i) => $i->total_tempo_segundos);
        $totalTempoFormatado = $this->formatarTempo($totalTempoSegundos);

        $porGabinete = $itens->groupBy(fn ($i) => $i->orcamento?->id_gabinete ?? 0)->map(function ($group, $idGab) {
            $primeiro = $group->first();
            return [
                'gabinete_nome' => $primeiro->orcamento?->gabinete?->nome ?? '—',
                'count' => $group->count(),
                'tempo_segundos' => $group->sum(fn ($i) => $i->total_tempo_segundos),
            ];
        })->values()->sortByDesc('count')->values()->all();

        foreach ($porGabinete as &$row) {
            $row['tempo_formatado'] = $this->formatarTempo($row['tempo_segundos']);
        }
        unset($row);

        $gabinetes = Gabinete::orderBy('nome')->get();
        $users = User::orderBy('name')->get();

        return view('gestao.relatorios', [
            'totalTrabalhos' => $totalTrabalhos,
            'totalTempoFormatado' => $totalTempoFormatado,
            'totalTempoSegundos' => $totalTempoSegundos,
            'porGabinete' => $porGabinete,
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
