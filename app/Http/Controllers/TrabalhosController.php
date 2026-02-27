<?php

namespace App\Http\Controllers;

use App\Models\OrcamentoHistorico;
use App\Models\OrcamentoItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrabalhosController extends Controller
{
    public function index(Request $request): View
    {
        $query = OrcamentoItem::query()
            ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
            ->with(['orcamento.requerente', 'orcamento.gabinete', 'orcamento.subcontratado', 'servico', 'user', 'subcontratado'])
            ->orderByRaw('CASE WHEN concluido_em IS NULL THEN 0 ELSE 1 END')
            ->orderBy('id');

        if ($request->filled('concluido')) {
            if ($request->input('concluido') === '1') {
                $query->whereNotNull('concluido_em');
            } else {
                $query->whereNull('concluido_em');
            }
        }

        if ($request->input('view') === 'kanban') {
            $pendentes = OrcamentoItem::query()
                ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
                ->whereNull('concluido_em')
                ->with(['orcamento.requerente', 'orcamento.gabinete', 'orcamento.processo', 'orcamento.subcontratado', 'servico', 'user', 'subcontratado'])
                ->orderBy('id')
                ->get();
            $concluidos = OrcamentoItem::query()
                ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
                ->whereNotNull('concluido_em')
                ->with(['orcamento.requerente', 'orcamento.gabinete', 'orcamento.processo', 'orcamento.subcontratado', 'servico', 'user', 'subcontratado'])
                ->orderByDesc('concluido_em')
                ->get();

            $users = User::orderBy('name')->get();

            return view('trabalhos.kanban', compact('pendentes', 'concluidos', 'users'));
        }

        $itens = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('trabalhos.index', compact('itens', 'users'));
    }

    public function markConcluido(Request $request, OrcamentoItem $item): JsonResponse
    {
        $item->load('orcamento');
        if ($item->orcamento->status !== 'em_execucao') {
            return response()->json(['ok' => false, 'message' => 'Orçamento não está em execução.'], 422);
        }

        if ($item->concluido_em) {
            $item->concluido_em = null;
            $item->save();

            return response()->json(['ok' => true, 'concluido' => false]);
        }

        $idUser = $request->input('id_user');
        $idSubcontratado = $request->input('id_subcontratado');
        if (empty($idUser) && empty($idSubcontratado)) {
            return response()->json(['ok' => false, 'message' => 'Atribua um técnico antes de marcar como concluído.'], 422);
        }
        if ($idUser && $idSubcontratado) {
            return response()->json(['ok' => false, 'message' => 'Selecione apenas um técnico (utilizador ou subcontratado).'], 422);
        }
        if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
            return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
        }

        $item->id_user = $idUser ?: null;
        $item->id_subcontratado = $idSubcontratado ?: null;
        $item->concluido_em = now();
        $item->save();

        $orcamento = $item->orcamento->fresh('itens');
        $passarPorFaturar = $orcamento->allItensConcluidos();
        if ($passarPorFaturar) {
            $orcamento->status = 'por_faturar';
            $orcamento->save();
            OrcamentoHistorico::create([
                'id_orcamento' => $orcamento->id,
                'status_anterior' => 'em_execucao',
                'status_novo' => 'por_faturar',
                'user_id' => $request->user()->id,
            ]);
        }

        return response()->json([
            'ok' => true,
            'concluido' => true,
            'orcamento_por_faturar' => $passarPorFaturar,
        ]);
    }
}
