<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Trabalhos
                </h2>
                <nav class="flex rounded-lg border border-gray-300 p-0.5 bg-gray-100" aria-label="Vista">
                    <a href="{{ route('trabalhos.index', request()->only(['concluido'])) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md bg-white text-gray-900 shadow border border-gray-200">
                        Lista
                    </a>
                    <a href="{{ route('trabalhos.index', array_merge(request()->only(['concluido']), ['view' => 'kanban'])) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:text-gray-900">
                        Kanban
                    </a>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 space-y-3">
                    <form method="get" action="{{ route('trabalhos.index') }}" class="flex flex-wrap items-end gap-2">
                        <input type="hidden" name="view" value="{{ request('view') }}">
                        <div>
                            <label for="concluido" class="block text-xs font-medium text-gray-500 mb-0.5">Estado</label>
                            <select name="concluido" id="concluido" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                                <option value="">Todos</option>
                                <option value="0" {{ request('concluido') === '0' ? 'selected' : '' }}>Pendentes</option>
                                <option value="1" {{ request('concluido') === '1' ? 'selected' : '' }}>Concluídos</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">
                            Filtrar
                        </button>
                        @if (request()->has('concluido'))
                            <a href="{{ route('trabalhos.index', request()->only(['view'])) }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">
                                Limpar
                            </a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Orçamento</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serviço / Descrição</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requerente</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gabinete</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($itens as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('orcamentos.edit', $item->orcamento) }}" class="font-mono text-epoc-primary hover:underline">{{ $item->orcamento->numero ?? '#' . $item->orcamento->id }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $item->servico?->nome ?? 'Serviço ocasional' }}
                                        @if ($item->descricao)
                                            <span class="text-gray-500 block text-xs">{{ Str::limit($item->descricao, 50) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->orcamento->requerente?->nome ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->orcamento->gabinete?->nome ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->tecnico_nome ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($item->concluido_em)
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-emerald-100 text-emerald-800">Concluído</span>
                                            <span class="text-gray-400 text-xs block">{{ $item->concluido_em->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-amber-100 text-amber-800">Pendente</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        @if ($item->concluido_em)
                                            <button type="button" class="trabalho-desfazer text-epoc-primary hover:text-epoc-primary-hover font-medium" data-url="{{ route('trabalhos.mark-concluido', $item) }}">Desfazer</button>
                                        @else
                                            @php
                                                $opts = $users->map(fn($u) => ['type' => 'user', 'id' => $u->id, 'name' => $u->name])->values()->all();
                                                if ($item->orcamento->subcontratado) {
                                                    $opts[] = ['type' => 'sub', 'id' => $item->orcamento->subcontratado->id, 'name' => $item->orcamento->subcontratado->nome . ' (subcontratado)'];
                                                }
                                            @endphp
                                            <button type="button" class="trabalho-marcar-concluido text-epoc-primary hover:text-epoc-primary-hover font-medium" data-url="{{ route('trabalhos.mark-concluido', $item) }}" data-tecnico-options="{{ json_encode($opts) }}">Marcar concluído</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum trabalho em execução. Os trabalhos aparecem aqui quando um orçamento está «Em execução».
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($itens->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $itens->links() }}
                    </div>
                @endif
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
                    openModal(this.dataset.url, JSON.parse(this.dataset.tecnicoOptions || '[]'));
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
