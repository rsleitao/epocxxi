<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcessoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Processo::query()
            ->withCount('orcamentos')
            ->with(['requerente', 'imovel'])
            ->orderByDesc('ano')
            ->orderByDesc('numero_sequencial');

        if ($request->filled('ano')) {
            $query->where('ano', (int) $request->input('ano'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('designacao', 'like', "%{$q}%")
                    ->orWhereHas('requerente', fn ($r) => $r->where('nome', 'like', "%{$q}%"))
                    ->orWhereHas('imovel', fn ($i) => $i->where('morada', 'like', "%{$q}%")->orWhere('nip', 'like', "%{$q}%"));
            });
        }

        $processos = $query->paginate(15)->withQueryString();
        $anos = Processo::query()->distinct()->pluck('ano')->sort()->values();

        return view('processos.index', compact('processos', 'anos'));
    }

    public function show(Processo $processo): View
    {
        $processo->load([
            'requerente',
            'imovel.distrito',
            'imovel.concelho',
            'imovel.freguesia',
            'imovel.tipoImovel',
            'orcamentos' => fn ($q) => $q->with(['gabinete', 'subcontratado', 'itens']),
        ]);

        $valorFaturado = $processo->orcamentos
            ->where('status', 'faturado')
            ->sum(fn ($o) => $o->total_com_iva);

        return view('processos.show', compact('processo', 'valorFaturado'));
    }
}
