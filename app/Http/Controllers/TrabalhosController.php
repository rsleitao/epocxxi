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
            ->with(['orcamento.requerente', 'orcamento.gabinete', 'orcamento.processo', 'orcamento.subcontratado', 'servico', 'user', 'subcontratado', 'tempoSegmentos']);

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

        $authUser = $request->user();
        $authId = $authUser->id;

        $estado = $request->input('estado');
        if (! in_array($estado, self::ESTADOS_ORDEM, true)) {
            return response()->json(['ok' => false, 'message' => 'Estado inválido.'], 422);
        }

        $idUser = $request->input('id_user');
        $idSubcontratado = $request->input('id_subcontratado');
        $notaPendente = $request->input('nota_pendente');

        if ($estado === 'em_execucao') {
            // Se não houver técnico atribuído ainda, e não foi enviado nenhum, assumir o próprio utilizador (toma posse do trabalho)
            if (! $item->id_user && ! $item->id_subcontratado && empty($idUser) && empty($idSubcontratado)) {
                $idUser = $authId;
            }

            $jaTemTecnico = $item->id_user || $item->id_subcontratado;
            $pediuTecnico = $idUser !== null && $idUser !== '' || $idSubcontratado !== null && $idSubcontratado !== '';
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
                $item->id_user = $idUser ? (int) $idUser : null;
                $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
            } elseif ($pediuTecnico) {
                // Reatribuição: já tem técnico mas pediu para alterar
                if ($idUser && $idSubcontratado) {
                    return response()->json(['ok' => false, 'message' => 'Selecione apenas um: técnico ou subcontratado.'], 422);
                }
                if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
                    return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
                }
                $item->id_user = $idUser ? (int) $idUser : null;
                $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
            }
            $item->nota_pendente = null;

            // Um técnico não pode ter outro trabalho em "Em execução" ao mesmo tempo
            $outroEmExecucao = OrcamentoItem::query()
                ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
                ->where('estado', 'em_execucao')
                ->where('id', '!=', $item->id);
            if ($item->id_user) {
                $outroEmExecucao->where('id_user', $item->id_user);
            } else {
                $outroEmExecucao->where('id_subcontratado', $item->id_subcontratado);
            }
            if ($outroEmExecucao->exists()) {
                return response()->json(['ok' => false, 'message' => 'Este técnico já tem outro trabalho em execução. Passe esse trabalho para Pendente ou Concluído primeiro.'], 422);
            }
        }

        if ($estado === 'pendente') {
            $jaTemTecnico = $item->id_user || $item->id_subcontratado;
            $pediuTecnicoPendente = $idUser !== null && $idUser !== '' || $idSubcontratado !== null && $idSubcontratado !== '';
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
                $item->id_user = $idUser ? (int) $idUser : null;
                $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
            } elseif ($pediuTecnicoPendente) {
                if ($idUser && $idSubcontratado) {
                    return response()->json(['ok' => false, 'message' => 'Selecione apenas um: técnico ou subcontratado.'], 422);
                }
                if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
                    return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
                }
                $item->id_user = $idUser ? (int) $idUser : null;
                $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
            }
            $item->nota_pendente = is_string($notaPendente) ? trim($notaPendente) : null;
        }

        if ($estado === 'em_espera') {
            $item->nota_pendente = null;
            $pediuTecnicoEmEspera = ($idUser !== null && $idUser !== '') || ($idSubcontratado !== null && $idSubcontratado !== '');
            if ($pediuTecnicoEmEspera) {
                if ($idUser && $idSubcontratado) {
                    return response()->json(['ok' => false, 'message' => 'Selecione apenas um: técnico ou subcontratado.'], 422);
                }
                if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
                    return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
                }
                $item->id_user = $idUser ? (int) $idUser : null;
                $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
            } else {
                $item->id_user = null;
                $item->id_subcontratado = null;
            }
            $item->tempoSegmentos()->delete();
        }

        if ($estado === 'concluido') {
            $needTecnico = ! $item->id_user && ! $item->id_subcontratado;
            $pediuTecnicoConcluido = ($idUser !== null && $idUser !== '') || ($idSubcontratado !== null && $idSubcontratado !== '');
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
                $item->id_user = $idUser ? (int) $idUser : null;
                $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
            } elseif ($pediuTecnicoConcluido) {
                if ($idUser && $idSubcontratado) {
                    return response()->json(['ok' => false, 'message' => 'Selecione apenas um técnico (utilizador ou subcontratado).'], 422);
                }
                if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
                    return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
                }
                $item->id_user = $idUser ? (int) $idUser : null;
                $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
            }
            $item->concluido_em = now();
            $item->nota_pendente = null;
        } else {
            $item->concluido_em = null;
        }

        // Apenas o técnico atribuído pode iniciar/pausar/retomar (Em execução / Pendente) — excepto em reatribuição (quem tem permissão pode trocar o técnico)
        $pediuTecnicoAlgum = ($idUser !== null && $idUser !== '') || ($idSubcontratado !== null && $idSubcontratado !== '');
        $soReatribuicao = ($item->estado === $estado) && $pediuTecnicoAlgum;
        if (in_array($estado, ['em_execucao', 'pendente'], true) && ! $soReatribuicao) {
            if (! $item->id_user || (int) $item->id_user !== (int) $authId) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Apenas o técnico atribuído pode iniciar/pausar este trabalho.',
                ], 422);
            }
        }

        // Cronómetro: fechar segmento aberto se estava em execução; abrir novo se passa a em execução
        if ($item->estado === 'em_execucao') {
            $item->fecharTempoAberto();
        }
        $item->estado = $estado;
        $item->save();
        if ($estado === 'em_execucao') {
            $item->abrirSegmentoTempo();
        }

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

        $item->load(['tempoSegmentos', 'user', 'subcontratado']);

        $payload = [
            'ok' => true,
            'estado' => $estado,
            'orcamento_por_faturar' => $orcamento_por_faturar,
            'tempo_total' => $item->tempo_total_formatado,
            'tempo_a_correr' => $item->hasTempoAberto(),
            'tempo_started_at' => $item->tempo_started_at,
            'tecnico_nome' => $item->tecnico_nome,
        ];
        if ($request->user()->hasPermission('trabalhos.view')) {
            $payload['trabalho_atual'] = $this->trabalhoAtualPayload($request->user());
        }

        return response()->json($payload);
    }

    /**
     * Dados do "trabalho atual" do utilizador (para o header) ou null.
     */
    private function trabalhoAtualPayload(\App\Models\User $user): ?array
    {
        $item = OrcamentoItem::query()
            ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
            ->whereIn('estado', ['em_execucao', 'pendente'])
            ->where('id_user', $user->id)
            ->with(['servico', 'orcamento.processo', 'tempoSegmentos'])
            ->orderBy('id')
            ->first();

        if (! $item) {
            return null;
        }

        $item->load(['tempoSegmentos', 'user', 'subcontratado']);

        return [
            'id' => $item->id,
            'estado' => $item->estado,
            'servico_nome' => $item->servico?->nome ?? 'Serviço ocasional',
            'processo_ref' => $item->orcamento->processo?->referencia,
            'orcamento_numero' => $item->orcamento->numero ?? '#'.$item->orcamento->id,
            'tempo_total_formatado' => $item->tempo_total_formatado,
            'tempo_a_correr' => $item->hasTempoAberto(),
            'tempo_started_at' => $item->tempo_started_at,
            'update_estado_url' => route('trabalhos.update-estado', $item),
            'target_estado' => $item->estado === 'em_execucao' ? 'pendente' : 'em_execucao',
        ];
    }

    public function markConcluido(Request $request, OrcamentoItem $item): JsonResponse
    {
        abort_unless($request->user()->hasPermission('trabalhos.edit'), 403);
        $item->load('orcamento');
        if ($item->orcamento->status !== 'em_execucao') {
            return response()->json(['ok' => false, 'message' => 'Orçamento não está em execução.'], 422);
        }

        if ($item->estado === 'concluido' && $item->concluido_em) {
            $item->fecharTempoAberto();
            $item->estado = 'em_espera';
            $item->concluido_em = null;
            $item->save();

            $payload = ['ok' => true, 'concluido' => false];
            if ($request->user()->hasPermission('trabalhos.view')) {
                $payload['trabalho_atual'] = $this->trabalhoAtualPayload($request->user());
            }
            return response()->json($payload);
        }

        $idUser = $request->input('id_user');
        $idSubcontratado = $request->input('id_subcontratado');
        $jaTemTecnico = $item->id_user || $item->id_subcontratado;

        if (! $jaTemTecnico && empty($idUser) && empty($idSubcontratado)) {
            return response()->json(['ok' => false, 'message' => 'Atribua um técnico antes de marcar como concluído.'], 422);
        }
        if ($idUser && $idSubcontratado) {
            return response()->json(['ok' => false, 'message' => 'Selecione apenas um técnico (utilizador ou subcontratado).'], 422);
        }
        if ($idSubcontratado && (int) $idSubcontratado !== (int) $item->orcamento->id_subcontratado) {
            return response()->json(['ok' => false, 'message' => 'O subcontratado deve ser o do orçamento.'], 422);
        }

        // Apenas o técnico atribuído (ou quem assume agora) pode concluir
        $authId = $request->user()->id;
        if ($item->id_user && (int) $item->id_user !== (int) $authId) {
            return response()->json([
                'ok' => false,
                'message' => 'Apenas o técnico atribuído pode concluir este trabalho.',
            ], 422);
        }

        if ($idUser || $idSubcontratado) {
            $item->id_user = $idUser ? (int) $idUser : null;
            $item->id_subcontratado = $idSubcontratado ? (int) $idSubcontratado : null;
        }
        $item->estado = 'concluido';
        $item->concluido_em = now();
        $item->nota_pendente = null;
        $item->fecharTempoAberto();
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

        $payload = [
            'ok' => true,
            'concluido' => true,
            'orcamento_por_faturar' => $passarPorFaturar,
        ];
        if ($request->user()->hasPermission('trabalhos.view')) {
            $payload['trabalho_atual'] = $this->trabalhoAtualPayload($request->user());
        }
        return response()->json($payload);
    }
}
