@php
    $estadoLabels = [
        'em_espera' => 'Em espera',
        'em_execucao' => 'Em execução',
        'pendente' => 'Pendente',
        'concluido' => 'Concluído',
    ];
    $estadoHeaderClasses = [
        'em_espera' => 'bg-gray-200 text-gray-800',
        'em_execucao' => 'bg-blue-700 text-white',
        'pendente' => 'bg-amber-200 text-amber-900',
        'concluido' => 'bg-emerald-700 text-white',
    ];
    $estadoBodyClasses = [
        'em_espera' => 'bg-gray-50',
        'em_execucao' => 'bg-blue-50',
        'pendente' => 'bg-amber-50',
        'concluido' => 'bg-emerald-50',
    ];
    $boardData = [];
    foreach ($estadosOrdem as $estado) {
        $boardData[$estado] = $byEstado[$estado]->map(function ($item) use ($users) {
            $opts = $users->map(fn ($u) => ['type' => 'user', 'id' => $u->id, 'name' => $u->name])->values()->all();
            if ($item->orcamento->subcontratado) {
                $opts[] = ['type' => 'sub', 'id' => $item->orcamento->subcontratado->id, 'name' => $item->orcamento->subcontratado->nome . ' (subcontratado)'];
            }
            return [
                'id' => $item->id,
                'estado' => $item->estado,
                'orcamento_numero' => $item->orcamento->numero ?? '#' . $item->orcamento->id,
                'orcamento_id' => $item->orcamento->id,
                'processo_ref' => $item->orcamento->processo?->referencia,
                'id_processo' => $item->orcamento->id_processo,
                'servico_nome' => $item->servico?->nome ?? 'Serviço ocasional',
                'tipo_trabalho' => $item->servico?->tipo_trabalho,
                'prazo_data' => $item->prazo_data?->format('d/m/Y'),
                'tecnico_nome' => $item->tecnico_nome,
                'concluido_em' => $item->concluido_em?->format('d/m/Y H:i'),
                'nota_pendente' => $item->nota_pendente,
                'tempo_total' => $item->tempo_total_formatado,
                'tempo_a_correr' => $item->hasTempoAberto(),
                'tempo_started_at' => $item->tempo_started_at,
                'tecnico_options' => $opts,
                'edit_orcamento_url' => route('orcamentos.edit', $item->orcamento),
                'processo_show_url' => $item->orcamento->processo ? route('processos.show', $item->orcamento->processo) : null,
            ];
        })->values()->all();
    }
@endphp
<style>
    .kanban-cursor-grab { cursor: grab !important; }
    .kanban-cursor-grab * { cursor: grab !important; }
    html.kanban-dragging, html.kanban-dragging *,
    body.kanban-dragging, body.kanban-dragging * { cursor: grabbing !important; }
</style>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Trabalhos
                </h2>
                <nav class="flex rounded-lg border border-gray-300 p-0.5 bg-gray-100" aria-label="Vista">
                    <a href="{{ route('trabalhos.index') }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:text-gray-900">
                        Lista
                    </a>
                    <a href="{{ route('trabalhos.index', ['view' => 'kanban']) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md bg-epoc-primary text-white hover:bg-epoc-primary-hover">
                        Kanban
                    </a>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-8 pb-20" x-data="kanbanTrabalhos(@js($boardData), @js($estadosOrdem), @js($estadoLabels))" x-init="startLiveTimer()">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-center overflow-x-auto pb-4 min-h-[calc(100vh-12rem)]">
                <div class="inline-flex gap-4" style="scrollbar-width: thin;">
                    <template x-for="estado in estadosOrdem" :key="estado">
                        <div class="flex-shrink-0 w-72 flex flex-col rounded-lg border-2 min-h-[400px] transition-colors duration-150 ease-out"
                             :class="{
                                 'border-epoc-primary bg-epoc-primary/5 shadow-sm': dragOverEstado === estado && dragCardEstado !== estado,
                                 'border-gray-200 bg-gray-50/50': dragOverEstado !== estado
                             }"
                             style="min-width: 18rem;"
                             @dragover.prevent="dragOverColumn($event, estado)"
                             @dragleave="dragLeaveColumn($event, estado)"
                             @drop.prevent="dropCard($event, estado)">
                            <div class="px-3 py-2 rounded-t-lg font-medium text-sm border-b-2 border-transparent"
                                 :class="estadoHeaderClass(estado)">
                                <span x-text="estadoLabels[estado] || estado"></span>
                                <span class="ml-1 opacity-80" x-text="'(' + (columns[estado]?.length || 0) + ')'"></span>
                            </div>
                            <div class="flex-1 p-2 space-y-2 overflow-y-auto min-h-[120px] rounded-b-lg border-2 border-gray-200 border-t-0"
                                 :class="estadoBodyClass(estado)">
                                <div x-show="dragOverEstado === estado && dragCardEstado !== estado"
                                     x-transition
                                     class="min-h-[4.5rem] rounded-lg border-2 border-dashed border-epoc-primary bg-epoc-primary/5 flex items-center justify-center mb-2">
                                    <span class="text-xs font-medium text-epoc-primary">Largue aqui</span>
                                </div>
                                <template x-for="card in (columns[estado] || [])" :key="card.id">
                                    <div class="px-4 py-3 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-epoc-primary/50 transition-all duration-200 ease-out kanban-cursor-grab"
                                         :class="{
                                             'opacity-30 scale-95 bg-gray-100 border-gray-300': dragCardId === card.id,
                                             'ring-2 ring-green-400 ring-offset-2': lastDroppedId === card.id
                                         }"
                                         draggable="true"
                                         @dragstart="dragStart($event, card.id, card.estado)"
                                         @dragend="dragEnd($event)">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <p class="text-xs font-mono text-gray-500">
                                                <a :href="card.edit_orcamento_url" class="text-epoc-primary hover:underline" @click.stop x-text="'Orç. ' + card.orcamento_numero"></a>
                                            </p>
                                            <template x-if="card.processo_show_url">
                                                <p class="text-xs font-mono text-gray-500">
                                                    <a :href="card.processo_show_url" class="text-epoc-primary hover:underline" @click.stop x-text="'Proc. ' + (card.processo_ref || '')"></a>
                                                </p>
                                            </template>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900 mt-1.5" x-text="card.servico_nome"></p>
                                        <p class="text-xs text-gray-600 mt-0.5" x-show="card.tipo_trabalho" x-text="'Tipo: ' + card.tipo_trabalho"></p>
                                        <dl class="mt-2 space-y-0.5 text-xs text-gray-600">
                                            <div x-show="card.prazo_data"><span class="text-gray-500">Prazo:</span> <span x-text="card.prazo_data"></span></div>
                                            <div><span class="text-gray-500">Técnico:</span> <span x-text="card.tecnico_nome || '—'"></span></div>
                                            <div><span class="text-gray-500">Tempo:</span> <span x-text="formatTempoCard(card)"></span><span x-show="card.tempo_a_correr" class="ml-1 text-blue-600 font-medium">(a correr)</span></div>
                                            <div x-show="card.nota_pendente" class="mt-1 p-1.5 bg-amber-50 rounded text-amber-800" x-text="card.nota_pendente"></div>
                                            <div x-show="card.estado === 'concluido' && card.concluido_em" class="text-emerald-600" x-text="'Concluído em ' + card.concluido_em"></div>
                                        </dl>
                                        <a :href="card.edit_orcamento_url" class="inline-block mt-2 text-xs font-medium text-epoc-primary hover:text-epoc-primary-hover kanban-cursor-grab" @click.stop>Ver orçamento →</a>
                                    </div>
                                </template>
                                <div x-show="!(columns[estado]?.length)" class="flex items-center justify-center h-24 text-sm text-gray-400 rounded border-2 border-dashed border-gray-300">
                                    Arraste aqui
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div x-show="dragCardId"
                 x-transition
                 class="mt-3 py-2 px-4 rounded-lg bg-epoc-primary/10 border border-epoc-primary/30 text-center text-sm text-gray-700"
                 x-cloak>
                <span class="font-medium text-epoc-primary">A arrastar.</span>
                <span class="ml-1">Largue na coluna desejada para alterar o estado.</span>
            </div>
        </div>

        {{-- Modal: técnico (Em execução ou Concluído sem técnico) --}}
        <div x-show="showModalTecnico"
             x-cloak
             x-transition
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
             @keydown.escape.window="cancelModalTecnico()">
            <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
                <h3 class="text-sm font-semibold text-gray-900" x-text="pendingTecnico?.estado === 'em_execucao' ? 'Atribuir técnico (Em execução)' : 'Atribuir técnico e concluir'"></h3>
                <p class="mt-1 text-xs text-gray-500">Selecione quem realizou ou vai realizar o trabalho.</p>
                <div class="mt-4">
                    <label for="tecnico-select" class="block text-xs font-medium text-gray-700 mb-1">Técnico</label>
                    <select id="tecnico-select" x-ref="tecnicoSelect" x-model="tecnicoSelectValue" class="w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                        <option value="">— Selecione —</option>
                        <template x-for="opt in (pendingTecnico?.tecnico_options || [])" :key="opt.type + '-' + opt.id">
                            <option :value="opt.type === 'user' ? 'user-' + opt.id : 'sub-' + opt.id" x-text="opt.name"></option>
                        </template>
                    </select>
                </div>
                <div class="mt-4 flex gap-2 justify-end">
                    <button type="button" @click="cancelModalTecnico()" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancelar</button>
                    <button type="button" @click="confirmModalTecnico()" class="px-3 py-2 text-sm font-medium text-white bg-epoc-primary rounded-md hover:bg-epoc-primary-hover">Confirmar</button>
                </div>
            </div>
        </div>

        {{-- Modal: nota pendente --}}
        <div x-show="showModalPendente"
             x-cloak
             x-transition
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
             @keydown.escape.window="cancelModalPendente()">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-sm font-semibold text-gray-900">Motivo de estar pendente</h3>
                <p class="mt-1 text-xs text-gray-500">Opcional: indique o que falta (ex.: informação do cliente).</p>
                <div class="mt-4">
                    <label for="nota-pendente" class="block text-xs font-medium text-gray-700 mb-1">Nota</label>
                    <textarea id="nota-pendente" x-model="pendingNotaText" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm" placeholder="Ex.: À espera de documentação..."></textarea>
                </div>
                <div class="mt-4 flex gap-2 justify-end">
                    <button type="button" @click="cancelModalPendente()" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancelar</button>
                    <button type="button" @click="confirmModalPendente()" class="px-3 py-2 text-sm font-medium text-white bg-epoc-primary rounded-md hover:bg-epoc-primary-hover">Mover para Pendente</button>
                </div>
            </div>
        </div>

        {{-- Modal: voltar para Em espera (apaga tempo e técnico) --}}
        <div x-show="showModalVoltarEspera"
             x-cloak
             x-transition
             class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
             @keydown.escape.window="showModalVoltarEspera = false; pendingVoltarEspera = null">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-sm font-semibold text-gray-900">Voltar para Em espera</h3>
                <p class="mt-2 text-sm text-gray-600">Ao voltar para <strong>Em espera</strong> serão apagados os registos de tempo e o técnico atribuído a este trabalho. Esta ação não pode ser desfeita.</p>
                <p class="mt-2 text-sm text-gray-600">Deseja continuar?</p>
                <div class="mt-4 flex gap-2 justify-end">
                    <button type="button" @click="showModalVoltarEspera = false; pendingVoltarEspera = null" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancelar</button>
                    <button type="button" @click="confirmVoltarEspera()" class="px-3 py-2 text-sm font-medium text-white bg-amber-600 rounded-md hover:bg-amber-700">Sim, voltar para Em espera</button>
                </div>
            </div>
        </div>

        {{-- Modal: orçamento Por faturar --}}
        <div x-show="showModalPorFaturar"
             x-cloak
             x-transition
             class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900">Orçamento atualizado</h3>
                        <p class="mt-1 text-sm text-gray-600">Todos os trabalhos deste orçamento foram concluídos. O orçamento passou a <strong>«Por faturar»</strong>.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" @click="showModalPorFaturar = false; location.reload()" class="px-4 py-2 text-sm font-medium text-white bg-epoc-primary rounded-md hover:bg-epoc-primary-hover">OK</button>
                </div>
            </div>
        </div>

        <div x-show="flashMessage"
             x-cloak
             x-transition
             class="fixed bottom-4 right-4 z-[200] max-w-sm shadow-lg rounded-lg overflow-hidden">
            <div class="flex items-center gap-3 px-4 py-3 bg-amber-500 text-white">
                <span x-text="flashMessage"></span>
                <button type="button" @click="flashMessage = null" class="p-1 rounded opacity-90 hover:opacity-100">×</button>
            </div>
        </div>
    </div>

    <script>
        function kanbanTrabalhos(initialColumns, estadosOrdemArray, estadoLabelsMap) {
            const estadoHeaderClasses = @json($estadoHeaderClasses);
            const estadoBodyClasses = @json($estadoBodyClasses);
            const urlEstado = '{{ url("trabalhos") }}';
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            return {
                estadosOrdem: estadosOrdemArray,
                estadoLabels: estadoLabelsMap,
                columns: JSON.parse(JSON.stringify(initialColumns)),
                dragCardId: null,
                dragCardEstado: null,
                dragCardData: null,
                dragOverEstado: null,
                lastDroppedId: null,
                showModalTecnico: false,
                pendingTecnico: null,
                showModalPendente: false,
                pendingPendente: null,
                pendingNotaText: '',
                showModalPorFaturar: false,
                showModalVoltarEspera: false,
                pendingVoltarEspera: null,
                flashMessage: null,
                tecnicoSelectValue: '',
                liveNow: Math.floor(Date.now() / 1000),

                startLiveTimer() {
                    const self = this;
                    setInterval(function() { self.liveNow = Math.floor(Date.now() / 1000); }, 10000);
                },
                formatTempoSegundos(s) {
                    if (s <= 0) return '0 min';
                    const h = Math.floor(s / 3600);
                    const m = Math.floor((s % 3600) / 60);
                    if (h > 0) return h + ' h ' + m + ' min';
                    return m + ' min';
                },
                formatTempoCard(card) {
                    if (card.tempo_a_correr && card.tempo_started_at) {
                        const s = Math.max(0, this.liveNow - card.tempo_started_at);
                        return this.formatTempoSegundos(s);
                    }
                    return (card.tempo_a_correr && (!card.tempo_total || card.tempo_total === '—')) ? '0 min' : (card.tempo_total || '—');
                },

                estadoHeaderClass(estado) {
                    return estadoHeaderClasses[estado] || 'bg-gray-200 text-gray-800';
                },
                estadoBodyClass(estado) {
                    return estadoBodyClasses[estado] || 'bg-gray-50';
                },

                dragStart(event, id, estado) {
                    this.lastDroppedId = null;
                    this.dragCardId = id;
                    this.dragCardEstado = estado;
                    const col = this.columns[estado] || [];
                    const idx = col.findIndex(c => c.id === id);
                    this.dragCardData = idx >= 0 ? { ...col[idx] } : null;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', id);
                    document.body.classList.add('kanban-dragging');
                    document.documentElement.classList.add('kanban-dragging');
                },

                dragEnd(event) {
                    document.body.classList.remove('kanban-dragging');
                    document.documentElement.classList.remove('kanban-dragging');
                    this.dragCardId = null;
                    this.dragCardEstado = null;
                    this.dragOverEstado = null;
                    this.dragCardData = null;
                    // Não limpar showModalTecnico/pendingTecnico (nem Pendente): o drop pode ter aberto o modal
                    // e o dragEnd dispara logo a seguir; limpar aqui fechava o modal à força.
                },

                dragOverColumn(event, estado) {
                    if (this.dragCardEstado === estado) return;
                    this.dragOverEstado = estado;
                },

                dragLeaveColumn(event, estado) {
                    const rect = event.currentTarget.getBoundingClientRect();
                    const x = event.clientX, y = event.clientY;
                    if (x <= rect.left || x >= rect.right || y <= rect.top || y >= rect.bottom) {
                        this.dragOverEstado = null;
                    }
                },

                dropCard(event, newEstado) {
                    this.dragOverEstado = null;
                    document.body.classList.remove('kanban-dragging');
                    document.documentElement.classList.remove('kanban-dragging');
                    if (!this.dragCardId || !this.dragCardData || this.dragCardEstado === newEstado) {
                        this.dragCardId = null;
                        this.dragCardEstado = null;
                        this.dragCardData = null;
                        return;
                    }

                    const cardData = this.dragCardData;
                    const oldEstado = this.dragCardEstado;

                    if (newEstado === 'em_execucao') {
                        if (cardData.tecnico_nome) {
                            this.doUpdateEstado(cardData, oldEstado, newEstado, null, null, null);
                        } else {
                            this.pendingTecnico = { ...cardData, targetEstado: newEstado };
                            this.tecnicoSelectValue = '';
                            this.showModalTecnico = true;
                            this.dragCardId = null;
                            this.dragCardEstado = null;
                            this.dragCardData = null;
                        }
                        return;
                    }
                    if (newEstado === 'pendente') {
                        if (cardData.tecnico_nome) {
                            this.pendingPendente = { ...cardData, targetEstado: newEstado };
                            this.pendingNotaText = cardData.nota_pendente || '';
                            this.showModalPendente = true;
                        } else {
                            this.pendingTecnico = { ...cardData, targetEstado: newEstado };
                            this.tecnicoSelectValue = '';
                            this.showModalTecnico = true;
                        }
                        this.dragCardId = null;
                        this.dragCardEstado = null;
                        this.dragCardData = null;
                        return;
                    }
                    if (newEstado === 'concluido') {
                        if (!cardData.tecnico_nome) {
                            this.pendingTecnico = { ...cardData, targetEstado: newEstado };
                            this.tecnicoSelectValue = '';
                            this.showModalTecnico = true;
                            this.dragCardId = null;
                            this.dragCardEstado = null;
                            this.dragCardData = null;
                            return;
                        }
                    }
                    if (newEstado === 'em_espera' && oldEstado !== 'em_espera') {
                        this.pendingVoltarEspera = { cardData, oldEstado };
                        this.showModalVoltarEspera = true;
                        this.dragCardId = null;
                        this.dragCardEstado = null;
                        this.dragCardData = null;
                        return;
                    }

                    this.doUpdateEstado(cardData, oldEstado, newEstado, null, null, null);
                },

                cancelModalTecnico() {
                    this.showModalTecnico = false;
                    this.pendingTecnico = null;
                },

                confirmModalTecnico() {
                    const val = this.tecnicoSelectValue;
                    if (!val || !this.pendingTecnico) {
                        this.cancelModalTecnico();
                        return;
                    }
                    const [type, id] = val.split('-');
                    const idUser = type === 'user' ? id : null;
                    const idSubcontratado = type === 'sub' ? id : null;
                    const cardData = this.pendingTecnico;
                    const targetEstado = cardData.targetEstado;
                    const tecnicoNome = (cardData.tecnico_options || []).find(o =>
                        (o.type === 'user' && o.id == idUser) || (o.type === 'sub' && o.id == idSubcontratado)
                    )?.name;
                    this.showModalTecnico = false;
                    this.pendingTecnico = null;
                    if (targetEstado === 'pendente') {
                        this.pendingPendente = { ...cardData, idUser, idSubcontratado, tecnico_nome: tecnicoNome || cardData.tecnico_nome, targetEstado: 'pendente' };
                        this.pendingNotaText = cardData.nota_pendente || '';
                        this.showModalPendente = true;
                    } else {
                        this.doUpdateEstado(cardData, cardData.estado, targetEstado, idUser, idSubcontratado, null);
                    }
                },

                cancelModalPendente() {
                    this.showModalPendente = false;
                    this.pendingPendente = null;
                    this.pendingNotaText = '';
                },

                confirmVoltarEspera() {
                    if (!this.pendingVoltarEspera) {
                        this.showModalVoltarEspera = false;
                        return;
                    }
                    const { cardData, oldEstado } = this.pendingVoltarEspera;
                    this.showModalVoltarEspera = false;
                    this.pendingVoltarEspera = null;
                    this.doUpdateEstado(cardData, oldEstado, 'em_espera', null, null, null);
                },

                confirmModalPendente() {
                    if (!this.pendingPendente) {
                        this.cancelModalPendente();
                        return;
                    }
                    const cardData = this.pendingPendente;
                    const targetEstado = cardData.targetEstado;
                    const nota = (this.pendingNotaText || '').trim();
                    const idUser = cardData.idUser ?? null;
                    const idSubcontratado = cardData.idSubcontratado ?? null;
                    this.showModalPendente = false;
                    this.pendingPendente = null;
                    this.pendingNotaText = '';
                    this.doUpdateEstado(cardData, cardData.estado, targetEstado, idUser, idSubcontratado, nota);
                },

                doUpdateEstado(cardData, oldEstado, newEstado, idUser, idSubcontratado, notaPendente) {
                    this.dragCardId = null;
                    this.dragCardEstado = null;
                    this.dragCardData = null;
                    this.dragOverEstado = null;
                    document.body.classList.remove('kanban-dragging');
                    document.documentElement.classList.remove('kanban-dragging');

                    this.columns[oldEstado] = (this.columns[oldEstado] || []).filter(c => c.id !== cardData.id);
                    const newCard = { ...cardData, estado: newEstado };
                    if (newEstado === 'em_espera') {
                        newCard.tecnico_nome = null;
                        newCard.tempo_total = '—';
                        newCard.tempo_a_correr = false;
                    }
                    if ((newEstado === 'em_execucao' || newEstado === 'pendente' || newEstado === 'concluido') && (idUser || idSubcontratado)) {
                        newCard.tecnico_nome = (cardData.tecnico_options || []).find(o =>
                            (o.type === 'user' && o.id == idUser) || (o.type === 'sub' && o.id == idSubcontratado)
                        )?.name || newCard.tecnico_nome;
                    }
                    if (newEstado === 'em_execucao') {
                        newCard.tempo_started_at = Math.floor(Date.now() / 1000);
                    }
                    if (newEstado !== 'em_execucao') {
                        newCard.tempo_started_at = null;
                    }
                    if (newEstado === 'pendente' && notaPendente !== null) newCard.nota_pendente = notaPendente;
                    if (newEstado !== 'pendente') newCard.nota_pendente = null;
                    if (newEstado === 'concluido') newCard.concluido_em = new Date().toLocaleString('pt-PT', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                    if (!this.columns[newEstado]) this.columns[newEstado] = [];
                    this.columns[newEstado] = [...this.columns[newEstado], newCard];

                    this.lastDroppedId = cardData.id;
                    setTimeout(() => { this.lastDroppedId = null; }, 1200);

                    const body = { estado: newEstado };
                    if (idUser) body.id_user = idUser;
                    if (idSubcontratado) body.id_subcontratado = idSubcontratado;
                    if (notaPendente !== null && notaPendente !== undefined) body.nota_pendente = notaPendente;

                    fetch(`${urlEstado}/${cardData.id}/estado`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify(body),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.ok) {
                            this.revertCard(cardData, oldEstado, newEstado);
                            this.flashMessage = data.message || 'Erro ao alterar estado.';
                            setTimeout(() => { this.flashMessage = null; }, 5000);
                        } else {
                            if (data.tempo_total !== undefined) {
                                this.columns[newEstado] = (this.columns[newEstado] || []).map(c =>
                                    c.id === cardData.id ? {
                                        ...c,
                                        tempo_total: data.tempo_total,
                                        tempo_a_correr: !!data.tempo_a_correr,
                                        tempo_started_at: data.tempo_started_at !== undefined ? data.tempo_started_at : c.tempo_started_at
                                    } : c
                                );
                            }
                            if (data.orcamento_por_faturar) this.showModalPorFaturar = true;
                        }
                    })
                    .catch(() => {
                        this.revertCard(cardData, oldEstado, newEstado);
                        this.flashMessage = 'Erro ao alterar estado.';
                        setTimeout(() => { this.flashMessage = null; }, 5000);
                    });
                },

                revertCard(cardData, oldEstado, newEstado) {
                    this.columns[newEstado] = (this.columns[newEstado] || []).filter(c => c.id !== cardData.id);
                    if (!this.columns[oldEstado]) this.columns[oldEstado] = [];
                    this.columns[oldEstado] = [...this.columns[oldEstado], { ...cardData, estado: oldEstado }];
                },
            };
        }
    </script>
</x-app-layout>
