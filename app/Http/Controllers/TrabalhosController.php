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
    private const ESTADOS_ORDEM = ['em_espera', 'em_execucao', 'pendente', 'concluido'];

    public function index(Request $request): View
    {
        if (! $request->user()->hasPermission('trabalhos.view')) {
            return redirect()->route('dashboard')
                ->with('warning', 'Não tem permissão para ver Trabalhos.');
        }

        $query = OrcamentoItem::query()
            ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
            ->with(['orcamento.requerente', 'orcamento.gabinete', 'orcamento.processo', 'orcamento.subcontratado', 'servico', 'user', 'subcontratado']);

        if ($request->input('view') === 'kanban') {
            $byEstado = [];
            foreach (self::ESTADOS_ORDEM as $estado) {
                $q = (clone $query)->where('estado', $estado);
                $byEstado[$estado] = $estado === 'concluido'
                    ? $q->orderByDesc('concluido_em')->get()
                    : $q->orderBy('id')->get();
            }
            $users = User::orderBy('name')->get();

            return view('trabalhos.kanban', [
                'byEstado' => $byEstado,
                'estadosOrdem' => self::ESTADOS_ORDEM,
                'users' => $users,
            ]);
        }

        $query->orderByRaw("FIELD(estado, 'em_espera', 'em_execucao', 'pendente', 'concluido')")
            ->orderBy('id');

        if ($request->filled('estado')) {
            $estado = $request->input('estado');
            if (in_array($estado, self::ESTADOS_ORDEM, true)) {
                $query->where('estado', $estado);
            }
        } elseif ($request->filled('concluido')) {
            if ($request->input('concluido') === '1') {
                $query->where('estado', 'concluido');
            } else {
                $query->where('estado', '!=', 'concluido');
            }
        }

        $itens = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('trabalhos.index', compact('itens', 'users'));
    }

    public function updateEstado(Request $request, OrcamentoItem $item): JsonResponse
    {
        abort_unless($request->user()->hasPermission('trabalhos.edit'), 403);
        $item->load('orcamento');
        if ($item->orcamento->status !== 'em_execucao') {
            return response()->json(['ok' => false, 'message' => 'Orçamento não está em execução.'], 422);
        }

        $estado = $request->input('estado');
        if (! in_array($estado, self::ESTADOS_ORDEM, true)) {
            return response()->json(['ok' => false, 'message' => 'Estado inválido.'], 422);
        }

        $idUser = $request->input('id_user');
        $idSubcontratado = $request->input('id_subcontratado');
        $notaPendente = $request->input('nota_pendente');

        if ($estado === 'em_execucao') {
            $jaTemTecnico = $item->id_user || $item->id_subcontratado;
            if (! $jaTemTecnico) {
                if (empty($idUser) && empty($idSubcontratado)) {
                    return response()->json(['ok' => false, 'message' => 'Selecione um técnico ou subcontratado para colocar em execução.'], 422);
                }
                if ($idUser && $idSubcontratado) {
                    return response()->json(['ok' => false, 'message' => 'Selecione apenas um: técnico ou subcontratado.'], 422);
                }
                if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
                    return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
                }
                $item->id_user = $idUser ?: null;
                $item->id_subcontratado = $idSubcontratado ?: null;
            }
            $item->nota_pendente = null;
        }

        if ($estado === 'pendente') {
            $jaTemTecnico = $item->id_user || $item->id_subcontratado;
            if (! $jaTemTecnico) {
                if (empty($idUser) && empty($idSubcontratado)) {
                    return response()->json(['ok' => false, 'message' => 'Selecione um técnico ou subcontratado para colocar em pendente.'], 422);
                }
                if ($idUser && $idSubcontratado) {
                    return response()->json(['ok' => false, 'message' => 'Selecione apenas um: técnico ou subcontratado.'], 422);
                }
                if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
                    return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
                }
                $item->id_user = $idUser ?: null;
                $item->id_subcontratado = $idSubcontratado ?: null;
            }
            $item->nota_pendente = is_string($notaPendente) ? trim($notaPendente) : null;
        }

        if ($estado === 'em_espera') {
            $item->nota_pendente = null;
        }

        if ($estado === 'concluido') {
            $needTecnico = ! $item->id_user && ! $item->id_subcontratado;
            if ($needTecnico) {
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
            }
            $item->concluido_em = now();
            $item->nota_pendente = null;
        } else {
            $item->concluido_em = null;
        }

        $item->estado = $estado;
        $item->save();

        $orcamento_por_faturar = false;
        if ($estado === 'concluido') {
            $orcamento = $item->orcamento->fresh('itens');
            if ($orcamento->allItensConcluidos()) {
                $orcamento->status = 'por_faturar';
                $orcamento->save();
                OrcamentoHistorico::create([
                    'id_orcamento' => $orcamento->id,
                    'status_anterior' => 'em_execucao',
                    'status_novo' => 'por_faturar',
                    'user_id' => $request->user()->id,
                ]);
                $orcamento_por_faturar = true;
            }
        }

        return response()->json([
            'ok' => true,
            'estado' => $estado,
            'orcamento_por_faturar' => $orcamento_por_faturar,
        ]);
    }

    public function markConcluido(Request $request, OrcamentoItem $item): JsonResponse
    {
        abort_unless($request->user()->hasPermission('trabalhos.edit'), 403);
        $item->load('orcamento');
        if ($item->orcamento->status !== 'em_execucao') {
            return response()->json(['ok' => false, 'message' => 'Orçamento não está em execução.'], 422);
        }

        if ($item->estado === 'concluido' && $item->concluido_em) {
            $item->estado = 'em_espera';
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
        $item->estado = 'concluido';
        $item->concluido_em = now();
        $item->nota_pendente = null;
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
