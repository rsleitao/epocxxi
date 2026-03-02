<?php

namespace App\Http\Controllers;

use App\Models\OrcamentoItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|JsonResponse
    {
        $mes = $request->input('mes', now()->format('Y-m'));
        $inicio = Carbon::parse($mes . '-01');

        $trabalhos = OrcamentoItem::query()
            ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
            ->whereNull('concluido_em')
            ->with(['orcamento.requerente', 'orcamento.gabinete', 'servico'])
            ->orderByRaw('CASE WHEN prazo_data IS NULL THEN 1 ELSE 0 END')
            ->orderBy('prazo_data')
            ->orderBy('id')
            ->get();

        $trabalhosPorData = [];
        foreach ($trabalhos as $item) {
            if ($item->prazo_data) {
                $key = $item->prazo_data->format('Y-m-d');
                if (! isset($trabalhosPorData[$key])) {
                    $trabalhosPorData[$key] = [];
                }
                $trabalhosPorData[$key][] = [
                    'id' => $item->id,
                    'servico' => $item->servico?->nome ?? 'Serviço ocasional',
                    'orcamento_numero' => $item->orcamento->numero ?? '#' . $item->orcamento->id,
                    'requerente' => $item->orcamento->requerente?->nome ?? '—',
                ];
            }
        }

        $datasComTrabalhos = array_keys($trabalhosPorData);
        $hoje = now()->format('Y-m-d');
        // Semana começa na segunda: ISO weekday 1=Segunda, 7=Domingo → offset 0-6
        $firstWeekday = $inicio->isoWeekday() - 1;

        $calendario = [
            'calendarioMes' => $inicio->format('Y-m'),
            'daysInMonth' => $inicio->daysInMonth(),
            'firstWeekday' => $firstWeekday,
            'datasComTrabalhos' => $datasComTrabalhos,
            'trabalhosPorData' => $trabalhosPorData,
            'mesAnoLabel' => $inicio->locale('pt')->translatedFormat('F Y'),
            'mesPrev' => $inicio->copy()->subMonth()->format('Y-m'),
            'mesNext' => $inicio->copy()->addMonth()->format('Y-m'),
            'hoje' => $hoje,
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($calendario);
        }

        $trabalhosPorDataJson = json_encode($trabalhosPorData);

        return view('dashboard', [
            'trabalhos' => $trabalhos,
            'trabalhosPorDataJson' => $trabalhosPorDataJson,
            'calendario' => $calendario,
        ]);
    }
}
