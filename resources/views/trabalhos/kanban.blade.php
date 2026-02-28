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
                       class="px-3 py-1.5 text-sm font-medium rounded-md bg-white text-gray-900 shadow border border-gray-200">
                        Kanban
                    </a>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Pendentes --}}
                <div class="bg-amber-50/50 rounded-xl border-2 border-amber-200 overflow-hidden">
                    <div class="px-4 py-3 bg-amber-200/80 border-b border-amber-300">
                        <h3 class="text-sm font-semibold text-amber-900">Pendentes</h3>
                        <p class="text-xs text-amber-800 mt-0.5">{{ $pendentes->count() }} trabalho(s)</p>
                    </div>
                    <div class="p-3 space-y-3 min-h-[200px] max-h-[70vh] overflow-y-auto">
                        @forelse ($pendentes as $item)
                            <div class="bg-white rounded-lg border border-amber-200 p-4 shadow-sm">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <p class="text-xs font-mono text-gray-500">
                                        <a href="{{ route('orcamentos.edit', $item->orcamento) }}" class="text-epoc-primary hover:underline">Orç. {{ $item->orcamento->numero ?? '#' . $item->orcamento->id }}</a>
                                    </p>
                                    @if ($item->orcamento->processo)
                                        <p class="text-xs font-mono text-gray-500">
                                            <a href="{{ route('processos.show', $item->orcamento->processo) }}" class="text-epoc-primary hover:underline">Proc. {{ $item->orcamento->processo->referencia }}</a>
                                        </p>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-gray-900 mt-1.5">{{ $item->servico?->nome ?? 'Serviço ocasional' }}</p>
                                @if ($item->servico?->tipo_trabalho)
                                    <p class="text-xs text-gray-600 mt-0.5"><span class="text-gray-500">Tipo:</span> {{ $item->servico->tipo_trabalho }}</p>
                                @endif
                                <dl class="mt-2 space-y-0.5 text-xs text-gray-600">
                                    @if ($item->prazo_data)
                                        <div><span class="text-gray-500">Prazo:</span> {{ $item->prazo_data->format('d/m/Y') }}</div>
                                    @endif
                                    <div><span class="text-gray-500">Técnico:</span> {{ $item->tecnico_nome ?? '—' }}</div>
                                </dl>
                                @php
                                    $opts = $users->map(fn($u) => ['type' => 'user', 'id' => $u->id, 'name' => $u->name])->values()->all();
                                    if ($item->orcamento->subcontratado) {
                                        $opts[] = ['type' => 'sub', 'id' => $item->orcamento->subcontratado->id, 'name' => $item->orcamento->subcontratado->nome . ' (subcontratado)'];
                                    }
                                @endphp
                                <button type="button"
                                        class="trabalho-marcar-concluido mt-3 w-full px-3 py-2 text-sm font-medium rounded-md bg-epoc-primary text-white hover:bg-epoc-primary-hover"
                                        data-url="{{ route('trabalhos.mark-concluido', $item) }}"
                                        data-tecnico-options="{{ json_encode($opts) }}">
                                    Marcar concluído
                                </button>
                            </div>
                        @empty
                            <div class="flex items-center justify-center h-24 text-sm text-amber-700/80">Nenhum trabalho pendente</div>
                        @endforelse
                    </div>
                </div>

                {{-- Concluídos --}}
                <div class="bg-emerald-50/50 rounded-xl border-2 border-emerald-200 overflow-hidden">
                    <div class="px-4 py-3 bg-emerald-200/80 border-b border-emerald-300">
                        <h3 class="text-sm font-semibold text-emerald-900">Concluídos</h3>
                        <p class="text-xs text-emerald-800 mt-0.5">{{ $concluidos->count() }} trabalho(s)</p>
                    </div>
                    <div class="p-3 space-y-3 min-h-[200px] max-h-[70vh] overflow-y-auto">
                        @forelse ($concluidos as $item)
                            <div class="bg-white rounded-lg border border-emerald-200 p-4 shadow-sm opacity-95">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <p class="text-xs font-mono text-gray-500">
                                        <a href="{{ route('orcamentos.edit', $item->orcamento) }}" class="text-epoc-primary hover:underline">Orç. {{ $item->orcamento->numero ?? '#' . $item->orcamento->id }}</a>
                                    </p>
                                    @if ($item->orcamento->processo)
                                        <p class="text-xs font-mono text-gray-500">
                                            <a href="{{ route('processos.show', $item->orcamento->processo) }}" class="text-epoc-primary hover:underline">Proc. {{ $item->orcamento->processo->referencia }}</a>
                                        </p>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-gray-900 mt-1.5">{{ $item->servico?->nome ?? 'Serviço ocasional' }}</p>
                                @if ($item->servico?->tipo_trabalho)
                                    <p class="text-xs text-gray-600 mt-0.5"><span class="text-gray-500">Tipo:</span> {{ $item->servico->tipo_trabalho }}</p>
                                @endif
                                <dl class="mt-2 space-y-0.5 text-xs text-gray-600">
                                    @if ($item->prazo_data)
                                        <div><span class="text-gray-500">Prazo:</span> {{ $item->prazo_data->format('d/m/Y') }}</div>
                                    @endif
                                    <div><span class="text-gray-500">Técnico:</span> {{ $item->tecnico_nome ?? '—' }}</div>
                                </dl>
                                <p class="text-xs text-emerald-600 mt-1.5">Concluído em {{ $item->concluido_em?->format('d/m/Y H:i') }}</p>
                                <button type="button"
                                        class="trabalho-desfazer mt-3 w-full px-3 py-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50"
                                        data-url="{{ route('trabalhos.mark-concluido', $item) }}">
                                    Desfazer
                                </button>
                            </div>
                        @empty
                            <div class="flex items-center justify-center h-24 text-sm text-emerald-700/80">Nenhum trabalho concluído</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: orçamento passou a Por faturar --}}
    <div id="modal-por-faturar" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4" aria-modal="true" role="dialog" aria-labelledby="modal-por-faturar-title">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex-1">
                    <h3 id="modal-por-faturar-title" class="text-sm font-semibold text-gray-900">Orçamento atualizado</h3>
                    <p class="mt-1 text-sm text-gray-600">Todos os trabalhos deste orçamento foram concluídos. O orçamento passou a <strong>«Por faturar»</strong>.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" id="modal-por-faturar-ok" class="px-4 py-2 text-sm font-medium text-white bg-epoc-primary rounded-md hover:bg-epoc-primary-hover">
                    OK
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: atribuir técnico e marcar concluído --}}
    <div id="modal-tecnico" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
            <h3 class="text-sm font-semibold text-gray-900">Atribuir técnico e marcar como concluído</h3>
            <p class="mt-1 text-xs text-gray-500">Selecione quem realizou o trabalho.</p>
            <div class="mt-4">
                <label for="tecnico-select" class="block text-xs font-medium text-gray-700 mb-1">Técnico</label>
                <select id="tecnico-select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                    <option value="">— Selecione —</option>
                </select>
            </div>
            <div class="mt-4 flex gap-2 justify-end">
                <button type="button" id="modal-tecnico-cancel" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancelar</button>
                <button type="button" id="modal-tecnico-confirm" class="px-3 py-2 text-sm font-medium text-white bg-epoc-primary rounded-md hover:bg-epoc-primary-hover">Concluir</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('modal-tecnico');
            const select = document.getElementById('tecnico-select');
            const cancelBtn = document.getElementById('modal-tecnico-cancel');
            const confirmBtn = document.getElementById('modal-tecnico-confirm');
            let currentUrl = null;

            function openModal(url, options) {
                currentUrl = url;
                select.innerHTML = '<option value="">— Selecione —</option>';
                (options || []).forEach(function(opt) {
                    const val = opt.type === 'user' ? 'user-' + opt.id : 'sub-' + opt.id;
                    select.appendChild(new Option(opt.name, val));
                });
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                select.value = '';
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                currentUrl = null;
            }

            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });

            document.querySelectorAll('.trabalho-marcar-concluido').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.dataset.url;
                    const options = JSON.parse(this.dataset.tecnicoOptions || '[]');
                    openModal(url, options);
                });
            });

            confirmBtn.addEventListener('click', async function() {
                const val = select.value;
                if (!val || !currentUrl) return;
                const [type, id] = val.split('-');
                const body = type === 'user' ? { id_user: id } : { id_subcontratado: id };
                confirmBtn.disabled = true;
                try {
                    const res = await fetch(currentUrl, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                    const data = await res.json();
                    if (data.ok) {
                        if (data.orcamento_por_faturar) {
                            closeModal();
                            document.getElementById('modal-por-faturar').classList.remove('hidden');
                            document.getElementById('modal-por-faturar').classList.add('flex');
                            return;
                        }
                        window.location.reload();
                    } else {
                        alert(data.message || 'Erro ao atualizar.');
                    }
                } catch (e) {
                    alert('Erro de ligação.');
                }
                confirmBtn.disabled = false;
                closeModal();
            });

            document.getElementById('modal-por-faturar-ok').addEventListener('click', function() {
                document.getElementById('modal-por-faturar').classList.add('hidden');
                document.getElementById('modal-por-faturar').classList.remove('flex');
                window.location.reload();
            });
            document.getElementById('modal-por-faturar').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    this.classList.remove('flex');
                    window.location.reload();
                }
            });

            document.querySelectorAll('.trabalho-desfazer').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const url = this.dataset.url;
                    this.disabled = true;
                    try {
                        const res = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (data.ok) window.location.reload();
                        else { alert(data.message || 'Erro.'); this.disabled = false; }
                    } catch (e) {
                        alert('Erro de ligação.');
                        this.disabled = false;
                    }
                });
            });
        })();
    </script>
</x-app-layout>
