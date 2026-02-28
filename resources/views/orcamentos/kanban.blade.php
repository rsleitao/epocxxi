@php
    $statusLabels = [
        'rascunho' => 'Rascunhos',
        'enviado' => 'Enviado',
        'em_execucao' => 'Em execução',
        'por_faturar' => 'Por faturar',
    ];
    $statusColors = [
        'rascunho' => 'bg-gray-100 border-gray-300',
        'enviado' => 'bg-blue-50 border-blue-200',
        'em_execucao' => 'bg-green-50 border-green-200',
        'por_faturar' => 'bg-amber-50 border-amber-200',
    ];
    $headerColors = [
        'rascunho' => 'bg-gray-200 text-gray-800',
        'enviado' => 'bg-blue-700 text-white',
        'em_execucao' => 'bg-green-700 text-white',
        'por_faturar' => 'bg-amber-200 text-amber-900',
    ];
    $statusOrdemVisiveis = $statusOrdem;
    $boardData = [];
    foreach ($statusOrdemVisiveis as $s) {
        $boardData[$s] = $orcamentosPorStatus->get($s, collect())->map(fn ($o) => [
            'id' => $o->id,
            'numero' => $o->numero ?? (string) $o->id,
            'designacao' => $o->designacao ?: '—',
            'requerente_nome' => $o->requerente?->nome ?? '—',
            'data_criacao' => $o->created_at?->format('d/m/Y'),
            'data_convertido' => $o->data_convertido?->format('d/m/Y'),
            'id_processo' => $o->id_processo,
            'edit_url' => route('orcamentos.edit', $o),
            'edit_label' => in_array($o->status, ['enviado', 'aceite', 'em_execucao', 'por_faturar', 'faturado']) ? 'Ver' : 'Editar',
            'destroy_url' => route('orcamentos.destroy', $o),
            'status' => $o->status,
        ])->values()->all();
    }
@endphp
<style>
    /* Cursores do Kanban: mão aberta nos cards, mão fechada ao arrastar */
    .kanban-cursor-grab { cursor: grab !important; }
    .kanban-cursor-grab * { cursor: grab !important; }
    html.kanban-dragging,
    html.kanban-dragging *,
    body.kanban-dragging,
    body.kanban-dragging * { cursor: grabbing !important; }
</style>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Orçamentos
                </h2>
                <nav class="flex rounded-lg border border-gray-300 p-0.5 bg-gray-100" aria-label="Vista">
                    <a href="{{ route('orcamentos.index', request()->only(['id_gabinete', 'q', 'ordenar'])) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:text-gray-900">
                        Lista
                    </a>
                    <a href="{{ route('orcamentos.index', array_merge(request()->only(['id_gabinete', 'q', 'ordenar']), ['view' => 'kanban'])) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md bg-white text-gray-900 shadow border border-gray-200">
                        Kanban
                    </a>
                </nav>
            </div>
            <a href="{{ route('orcamentos.create') }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo orçamento
            </a>
        </div>
    </x-slot>

    <div class="py-6 pb-20" x-data="kanbanOrcamentos(@js($boardData), @js($statusOrdemVisiveis))">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-center" x-data="{
                form: null,
                debounceTimer: null,
                init() {
                    this.form = this.$el.querySelector('form');
                    this.form.querySelector('#id_gabinete').addEventListener('change', () => this.form.submit());
                    this.form.querySelector('#ordenar').addEventListener('change', () => this.form.submit());
                    const q = this.form.querySelector('#q');
                    q.addEventListener('input', () => {
                        clearTimeout(this.debounceTimer);
                        this.debounceTimer = setTimeout(() => this.form.submit(), 400);
                    });
                }
            }">
                <form method="get" action="{{ route('orcamentos.index') }}" class="flex flex-wrap items-end gap-2 justify-center">
                    <input type="hidden" name="view" value="kanban">
                    <div>
                        <label for="id_gabinete" class="block text-xs font-medium text-gray-500 mb-0.5">Gabinete</label>
                        <select name="id_gabinete" id="id_gabinete" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm w-48">
                            <option value="">Todos</option>
                            @foreach ($gabinetes as $g)
                                <option value="{{ $g->id }}" {{ request('id_gabinete') == $g->id ? 'selected' : '' }}>{{ $g->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="ordenar" class="block text-xs font-medium text-gray-500 mb-0.5">Ordenar</label>
                        <select name="ordenar" id="ordenar" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm w-40">
                            <option value="recente" {{ ($ordenar ?? 'recente') === 'recente' ? 'selected' : '' }}>Mais recente</option>
                            <option value="antigo" {{ ($ordenar ?? '') === 'antigo' ? 'selected' : '' }}>Mais antigo</option>
                        </select>
                    </div>
                    <div>
                        <label for="q" class="block text-xs font-medium text-gray-500 mb-0.5">Pesquisar</label>
                        <input type="search" name="q" id="q" value="{{ request('q') }}"
                               placeholder="Designação ou requerente..."
                               class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary w-56 text-sm">
                    </div>
                    @if (request()->hasAny(['id_gabinete', 'q']) || request('ordenar') === 'antigo')
                        <a href="{{ route('orcamentos.index', ['view' => 'kanban']) }}" class="px-4 py-2 text-gray-600 text-sm hover:underline self-end">
                            Limpar filtros
                        </a>
                    @endif
                </form>
            </div>
            <p class="text-center text-xs text-gray-500 mb-2">Filtros aplicam-se ao alterar (pesquisa com pequeno atraso).</p>

            <div class="flex justify-center overflow-x-auto pb-4 min-h-[calc(100vh-14rem)]">
                <div class="inline-flex gap-4" style="scrollbar-width: thin;">
                    <template x-for="status in statusOrdem" :key="status">
                        <div class="flex-shrink-0 w-72 flex flex-col rounded-lg border-2 min-h-[400px] transition-colors duration-150 ease-out"
                             :class="{
                                 // Hover de coluna: apenas muda cor/borda, sem aumentar o tamanho
                                 'border-epoc-primary bg-epoc-primary/5 shadow-sm': dragOverStatus === status && dragOrcamentoStatus !== status,
                                 'border-gray-200 bg-gray-50/50': dragOverStatus !== status
                             }"
                             style="min-width: 18rem;"
                             @dragover.prevent="dragOverColumn($event, status)"
                             @dragleave="dragLeaveColumn($event, status)"
                             @drop.prevent="dropCard($event, status)">
                            <div class="px-3 py-2 rounded-t-lg font-medium text-sm border-b-2 border-transparent"
                                 :class="{
                                     'bg-gray-200 text-gray-800': status === 'rascunho',
                                     'bg-blue-700 text-white': status === 'enviado',
                                     'bg-green-700 text-white': status === 'em_execucao',
                                     'bg-amber-200 text-amber-900': status === 'por_faturar'
                                 }">
                                <span x-text="statusLabels[status] || status"></span>
                                <span class="ml-1 opacity-80" x-text="'(' + (columns[status]?.length || 0) + ')'"></span>
                            </div>
                            <div class="flex-1 p-2 space-y-2 overflow-y-auto min-h-[120px] rounded-b-lg border-t-0 border-2 border-gray-200 border-t-0"
                                 :class="{
                                     'bg-gray-100': status === 'rascunho',
                                     'bg-blue-100': status === 'enviado',
                                     'bg-green-50': status === 'em_execucao',
                                     'bg-amber-50': status === 'por_faturar'
                                 }">
                                <!-- Caixa tracejada que aparece ao passar com o card para mostrar onde vai ficar -->
                                <div x-show="dragOverStatus === status && dragOrcamentoStatus !== status"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     class="min-h-[4.5rem] rounded-lg border-2 border-dashed border-epoc-primary bg-epoc-primary/5 flex items-center justify-center mb-2">
                                    <span class="text-xs font-medium text-epoc-primary">Largue aqui</span>
                                </div>
                                <template x-for="card in (columns[status] || [])" :key="card.id">
                                    <div class="px-4 py-3 bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-epoc-primary/50 transition-all duration-200 ease-out kanban-cursor-grab"
                                         :class="{
                                             'opacity-30 scale-95 bg-gray-100 border-gray-300': dragOrcamentoId === card.id,
                                             'ring-2 ring-green-400 ring-offset-2': lastDroppedId === card.id
                                         }"
                                         draggable="true"
                                         @dragstart="dragStart($event, card.id, card.status)"
                                         @dragend="dragEnd($event)">
                                        <p class="text-xs font-mono text-gray-500" x-text="card.numero"></p>
                                        <p class="text-xs text-gray-400" x-text="'Criado em ' + (card.data_criacao || '—')"></p>
                                        <p class="text-sm font-medium text-gray-900 mt-0.5 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" x-text="card.designacao"></p>
                                        <p class="text-xs text-gray-600 mt-1" x-text="card.requerente_nome"></p>
                                        <p class="text-xs text-gray-500 mt-0.5" x-show="card.data_convertido" x-text="'Aceite em ' + card.data_convertido"></p>
                                        <a :href="card.edit_url" class="inline-block mt-2 text-xs font-medium text-epoc-primary hover:text-epoc-primary-hover kanban-cursor-grab" x-text="card.edit_label + ' →'"></a>
                                    </div>
                                </template>
                                <div x-show="!(columns[status]?.length)" class="flex items-center justify-center h-24 text-sm text-gray-400 rounded border-2 border-dashed border-gray-300">
                                    Arraste aqui
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Dica ao arrastar -->
            <div x-show="dragOrcamentoId"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mt-3 py-2 px-4 rounded-lg bg-epoc-primary/10 border border-epoc-primary/30 text-center text-sm text-gray-700"
                 x-cloak>
                <span class="font-medium text-epoc-primary">A arrastar.</span>
                <span class="ml-1" x-show="dragOrcamentoStatus === 'rascunho'">Largue em <strong>Enviado</strong> ou use <strong>Remover</strong> em baixo.</span>
                <span class="ml-1" x-show="dragOrcamentoStatus === 'enviado'">Largue em <strong>Em execução</strong> ou use os botões em baixo.</span>
                <span class="ml-1" x-show="dragOrcamentoStatus === 'em_execucao' || dragOrcamentoStatus === 'por_faturar'">Use o botão em baixo para alterar o estado.</span>
            </div>
        </div>

        <!-- Modal: ao cancelar orçamento com processo, escolher manter ou apagar processo -->
        <div x-show="showCancelModal"
             x-cloak
             x-transition
             class="fixed inset-0 z-[210] flex items-center justify-center bg-black/50 p-4"
             @keydown.escape.window="showCancelModal = false; pendingCancelCard = null">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-sm font-semibold text-gray-900">Cancelar orçamento</h3>
                <p class="mt-2 text-sm text-gray-700">Este orçamento tem processo associado. O que pretende fazer?</p>
                <p class="mt-2 text-sm text-amber-700" x-show="pendingCancelCard && pendingCancelCard.status === 'em_execucao'">
                    Os trabalhos (itens em execução) deste orçamento deixarão de constar na lista de Trabalhos.
                </p>
                <div class="mt-4 space-y-2">
                    <button type="button"
                            class="w-full px-4 py-3 text-left text-sm rounded-lg border border-gray-200 hover:bg-gray-50"
                            @click="doCancelado(false)">
                        Manter processo no histórico (o cancelamento fica registado)
                    </button>
                    <button type="button"
                            class="w-full px-4 py-3 text-left text-sm rounded-lg border border-amber-200 bg-amber-50 hover:bg-amber-100"
                            @click="doCancelado(true)">
                        Apagar processo (fica apenas o registo de orçamento cancelado)
                    </button>
                </div>
                <div class="mt-4 pt-3 border-t">
                    <button type="button" class="text-sm text-gray-500 hover:text-gray-700"
                            @click="showCancelModal = false; pendingCancelCard = null">
                        Voltar
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash warning (mesmo estilo que os sucessos do layout) -->
        <div x-show="flashWarning"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-4"
             class="fixed bottom-4 right-4 z-[200] max-w-sm w-full shadow-lg rounded-lg overflow-hidden">
            <div class="flex items-center gap-3 px-4 py-3 bg-amber-500 text-white hover:bg-amber-400">
                <span class="flex-shrink-0">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </span>
                <p class="flex-1 text-sm font-medium" x-text="flashWarning"></p>
                <button type="button" @click="flashWarning = null" class="flex-shrink-0 p-1 rounded opacity-90 hover:opacity-100 focus:outline-none">
                    <span class="sr-only">Fechar</span>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>

        <!-- Botões em baixo: consoante a coluna do card a arrastar; hover/scale quando se passa o card por cima -->
        <div x-show="dragOrcamentoId"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-6 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed bottom-0 left-0 right-0 z-50 p-4 flex justify-center">
            <div class="flex flex-wrap gap-3 justify-center items-center">
                <template x-if="dragOrcamentoStatus === 'rascunho'">
                    <div class="transition-all duration-200 ease-out origin-center"
                         :class="dragOverButton === 'remover' ? 'scale-110' : 'scale-100'"
                         @dragover.prevent="dragOverButton = 'remover'"
                         @dragleave="dragLeaveButton($event)"
                         @drop.prevent="actionRemover(); dragOverButton = null">
                        <button type="button"
                                class="rounded-xl border-2 px-10 py-4 shadow-lg font-medium text-sm transition-all duration-200 min-w-[12rem]"
                                :class="dragOverButton === 'remover' ? 'border-red-400 bg-red-500 ring-2 ring-red-300 ring-offset-2 text-white' : 'border-red-500 bg-red-600 hover:bg-red-500 text-white'"
                                @click="actionRemover()">
                            ⊗ Remover orçamento
                        </button>
                    </div>
                </template>
                <template x-if="dragOrcamentoStatus === 'enviado'">
                    <div class="flex gap-3">
                        <div class="transition-all duration-200 ease-out origin-center"
                             :class="dragOverButton === 'recusar' ? 'scale-110' : 'scale-100'"
                             @dragover.prevent="dragOverButton = 'recusar'"
                             @dragleave="dragLeaveButton($event)"
                             @drop.prevent="actionRecusar(); dragOverButton = null">
                            <button type="button"
                                    class="rounded-xl border-2 px-10 py-4 shadow-lg font-medium text-sm transition-all duration-200 min-w-[10rem]"
                                    :class="dragOverButton === 'recusar' ? 'border-red-400 bg-red-500 ring-2 ring-red-300 ring-offset-2 text-white' : 'border-red-500 bg-red-600 hover:bg-red-500 text-white'"
                                    @click="actionRecusar()">
                                Recusar
                            </button>
                        </div>
                        <div class="transition-all duration-200 ease-out origin-center"
                             :class="dragOverButton === 'aceite' ? 'scale-110' : 'scale-100'"
                             @dragover.prevent="dragOverButton = 'aceite'"
                             @dragleave="dragLeaveButton($event)"
                             @drop.prevent="actionAceite(); dragOverButton = null">
                            <button type="button"
                                    class="rounded-xl border-2 px-10 py-4 shadow-lg font-medium text-sm transition-all duration-200 min-w-[10rem]"
                                    :class="dragOverButton === 'aceite' ? 'border-green-500 bg-green-500 ring-2 ring-green-300 ring-offset-2 text-white' : 'border-green-600 bg-green-600 hover:bg-green-500 text-white'"
                                    @click="actionAceite()">
                                Aceite
                            </button>
                        </div>
                    </div>
                </template>
                <template x-if="dragOrcamentoStatus === 'em_execucao'">
                    <div class="transition-all duration-200 ease-out origin-center"
                         :class="dragOverButton === 'cancelado' ? 'scale-110' : 'scale-100'"
                         @dragover.prevent="dragOverButton = 'cancelado'"
                         @dragleave="dragLeaveButton($event)"
                         @drop.prevent="actionCancelado(); dragOverButton = null">
                        <button type="button"
                                class="rounded-xl border-2 px-10 py-4 shadow-lg font-medium text-sm transition-all duration-200 min-w-[10rem]"
                                :class="dragOverButton === 'cancelado' ? 'border-gray-400 bg-gray-500 ring-2 ring-gray-300 ring-offset-2 text-white' : 'border-gray-500 bg-gray-600 hover:bg-gray-500 text-white'"
                                @click="actionCancelado()">
                            Cancelado
                        </button>
                    </div>
                </template>
                <template x-if="dragOrcamentoStatus === 'por_faturar'">
                    <div class="transition-all duration-200 ease-out origin-center"
                         :class="dragOverButton === 'faturado' ? 'scale-110' : 'scale-100'"
                         @dragover.prevent="dragOverButton = 'faturado'"
                         @dragleave="dragLeaveButton($event)"
                         @drop.prevent="actionFaturado(); dragOverButton = null">
                        <button type="button"
                                class="rounded-xl border-2 px-10 py-4 shadow-lg font-medium text-sm transition-all duration-200 min-w-[10rem]"
                                :class="dragOverButton === 'faturado' ? 'border-emerald-400 bg-emerald-500 ring-2 ring-emerald-300 ring-offset-2 text-white' : 'border-emerald-500 bg-emerald-600 hover:bg-emerald-500 text-white'"
                                @click="actionFaturado()">
                            € Faturado
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        const statusLabels = @json($statusLabels);

        function kanbanOrcamentos(initialColumns, statusOrdemArray) {
            return {
                statusOrdem: statusOrdemArray,
                statusLabels: statusLabels,
                columns: JSON.parse(JSON.stringify(initialColumns)),
                dragOrcamentoId: null,
                dragOrcamentoStatus: null,
                dragOverStatus: null,
                dragOverButton: null,
                dragCardData: null,
                lastDroppedId: null,
                _cursorInterval: null,
                flashWarning: null,
                _flashTimeout: null,
                showCancelModal: false,
                pendingCancelCard: null,

                dragLeaveButton(event) {
                    if (!event.currentTarget.contains(event.relatedTarget)) {
                        this.dragOverButton = null;
                    }
                },

                dragStart(event, id, status) {
                    // Garantia defensiva: limpa qualquer estado visual anterior
                    this.clearDragCursor();
                    this.lastDroppedId = null;
                    this.dragOrcamentoId = id;
                    this.dragOrcamentoStatus = status;
                    const col = this.columns[status];
                    const idx = col.findIndex(c => c.id === id);
                    this.dragCardData = idx >= 0 ? { ...col[idx] } : null;
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', id);
                    document.body.classList.add('kanban-dragging');
                    document.documentElement.classList.add('kanban-dragging');
                },

                clearDragCursor() {
                    document.body.classList.remove('kanban-dragging');
                    document.documentElement.classList.remove('kanban-dragging');
                },

                dragEnd(event) {
                    this.clearDragCursor();
                    this.dragOrcamentoId = null;
                    this.dragOrcamentoStatus = null;
                    this.dragOverStatus = null;
                    this.dragOverButton = null;
                    this.dragCardData = null;
                    this.showCancelModal = false;
                    this.pendingCancelCard = null;
                },

                dragOverColumn(event, status) {
                    if (this.dragOrcamentoStatus === status) return;
                    this.dragOverStatus = status;
                },

                dragLeaveColumn(event, status) {
                    const rect = event.currentTarget.getBoundingClientRect();
                    const x = event.clientX;
                    const y = event.clientY;
                    if (x <= rect.left || x >= rect.right || y <= rect.top || y >= rect.bottom) {
                        this.dragOverStatus = null;
                    }
                },

                dropCard(event, newStatus) {
                    this.dragOverStatus = null;
                    this.clearDragCursor();
                    // Só permitir arrastar: rascunho→enviado, enviado→em_execucao
                    const allowed = { rascunho: ['enviado'], enviado: ['em_execucao'] };
                    const permitidos = allowed[this.dragOrcamentoStatus];
                    if (!permitidos || !permitidos.includes(newStatus)) {
                        if (this.dragOrcamentoStatus !== newStatus) {
                            this.showFlashWarning('Transição não permitida. Use os botões em baixo para Recusar, Aceite, Cancelado ou Faturado.');
                        }
                        this.dragOrcamentoId = null;
                        this.dragOrcamentoStatus = null;
                        this.dragCardData = null;
                        return;
                    }

                    if (this.dragOrcamentoStatus === newStatus) {
                        this.dragOrcamentoId = null;
                        this.dragOrcamentoStatus = null;
                        this.dragCardData = null;
                        return;
                    }
                    if (!this.dragOrcamentoId || !this.dragCardData) return;

                    const oldStatus = this.dragOrcamentoStatus;
                    const cardData = this.dragCardData;

                    this.lastDroppedId = null;

                    // Remover da coluna antiga
                    this.columns[oldStatus] = this.columns[oldStatus].filter(c => c.id !== this.dragOrcamentoId);
                    // Inserir na nova coluna com status atualizado
                    const newCard = { ...cardData, status: newStatus };
                    if (!this.columns[newStatus]) this.columns[newStatus] = [];
                    this.columns[newStatus] = [...this.columns[newStatus], newCard];

                    this.dragOrcamentoId = null;
                    this.dragOrcamentoStatus = null;
                    this.dragCardData = null;

                    this.lastDroppedId = cardData.id;
                    const self = this;
                    setTimeout(() => { self.lastDroppedId = null; }, 1200);

                    const url = '{{ url("orcamentos") }}/' + cardData.id + '/status';
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ status: newStatus }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.ok) {
                            this.lastDroppedId = null;
                            this.revertCard(cardData, oldStatus, newStatus);
                            this.showFlashWarning(data.message || 'Erro ao alterar estado.');
                        }
                    })
                    .catch(() => {
                        this.lastDroppedId = null;
                        this.revertCard(cardData, oldStatus, newStatus);
                        this.showFlashWarning('Erro ao alterar estado.');
                    });
                },

                revertCard(cardData, oldStatus, newStatus) {
                    this.columns[newStatus] = this.columns[newStatus].filter(c => c.id !== cardData.id);
                    if (!this.columns[oldStatus]) this.columns[oldStatus] = [];
                    this.columns[oldStatus] = [...this.columns[oldStatus], { ...cardData, status: oldStatus }];
                },

                showFlashWarning(message) {
                    if (this._flashTimeout) clearTimeout(this._flashTimeout);
                    this.flashWarning = message;
                    this._flashTimeout = setTimeout(() => {
                        this.flashWarning = null;
                        this._flashTimeout = null;
                    }, 5000);
                },

                actionRemover() {
                    if (!this.dragOrcamentoId || !this.dragCardData) return;
                    const cardData = this.dragCardData;
                    const oldStatus = this.dragOrcamentoStatus;
                    const id = this.dragOrcamentoId;
                    this.columns[oldStatus] = this.columns[oldStatus].filter(c => c.id !== id);
                    this.dragOrcamentoId = null;
                    this.dragOrcamentoStatus = null;
                    this.dragCardData = null;
                    this.clearDragCursor();
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch((cardData.destroy_url || '{{ url("orcamentos") }}/' + id), {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data || !data.ok) {
                            this.columns[oldStatus] = [...(this.columns[oldStatus] || []), { ...cardData, status: oldStatus }];
                            this.showFlashWarning('Erro ao remover.');
                        }
                    })
                    .catch(() => {
                        this.columns[oldStatus] = [...(this.columns[oldStatus] || []), { ...cardData, status: oldStatus }];
                        this.showFlashWarning('Erro ao remover.');
                    });
                },

                actionRecusar() { this.actionSetStatus('recusado', false); },
                actionAceite() { this.actionSetStatus('em_execucao', true); },
                actionCancelado() {
                    if (this.dragCardData && this.dragCardData.id_processo) {
                        this.pendingCancelCard = { ...this.dragCardData };
                        this.showCancelModal = true;
                        this.dragOrcamentoId = null;
                        this.dragOrcamentoStatus = null;
                        this.dragOverButton = null;
                        this.dragCardData = null;
                        this.clearDragCursor();
                        return;
                    }
                    if (this.dragCardData && this.dragCardData.status === 'em_execucao' && !confirm('Os trabalhos deste orçamento deixarão de constar na lista de Trabalhos. Continuar?')) return;
                    this.actionSetStatus('cancelado', false);
                },
                doCancelado(apagarProcesso) {
                    const cardData = this.pendingCancelCard;
                    if (!cardData) { this.showCancelModal = false; this.pendingCancelCard = null; return; }
                    const oldStatus = cardData.status;
                    const id = cardData.id;
                    this.columns[oldStatus] = (this.columns[oldStatus] || []).filter(c => c.id !== id);
                    this.showCancelModal = false;
                    this.pendingCancelCard = null;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch('{{ url("orcamentos") }}/' + cardData.id + '/status', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ status: 'cancelado', apagar_processo: !!apagarProcesso }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.ok) {
                            this.columns[oldStatus] = [...(this.columns[oldStatus] || []), { ...cardData, status: oldStatus }];
                            this.showFlashWarning(data.message || 'Erro ao cancelar.');
                        }
                    })
                    .catch(() => {
                        this.columns[oldStatus] = [...(this.columns[oldStatus] || []), { ...cardData, status: oldStatus }];
                        this.showFlashWarning('Erro ao cancelar.');
                    });
                },
                actionFaturado() { this.actionSetStatus('faturado', false); },

                actionSetStatus(newStatus, moveToColumn) {
                    if (!this.dragOrcamentoId || !this.dragCardData) return;
                    const oldStatus = this.dragOrcamentoStatus;
                    const cardData = this.dragCardData;
                    const id = this.dragOrcamentoId;
                    this.columns[oldStatus] = this.columns[oldStatus].filter(c => c.id !== id);
                    this.dragOrcamentoId = null;
                    this.dragOrcamentoStatus = null;
                    this.dragCardData = null;
                    this.clearDragCursor();
                    if (moveToColumn) {
                        if (!this.columns[newStatus]) this.columns[newStatus] = [];
                        this.columns[newStatus] = [...this.columns[newStatus], { ...cardData, status: newStatus }];
                    }
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch('{{ url("orcamentos") }}/' + cardData.id + '/status', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ status: newStatus }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.ok) {
                            this.columns[oldStatus] = [...(this.columns[oldStatus] || []), { ...cardData, status: oldStatus }];
                            if (moveToColumn) this.columns[newStatus] = this.columns[newStatus].filter(c => c.id !== id);
                            this.showFlashWarning(data.message || 'Erro ao alterar estado.');
                        }
                    })
                    .catch(() => {
                        this.columns[oldStatus] = [...(this.columns[oldStatus] || []), { ...cardData, status: oldStatus }];
                        if (moveToColumn) this.columns[newStatus] = this.columns[newStatus].filter(c => c.id !== id);
                        this.showFlashWarning('Erro ao alterar estado.');
                    });
                },
            };
        }
    </script>
</x-app-layout>
