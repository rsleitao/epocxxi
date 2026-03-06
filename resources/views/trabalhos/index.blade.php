<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Trabalhos
                </h2>
                <nav class="flex rounded-lg border border-gray-300 p-0.5 bg-gray-100" aria-label="Vista">
                    <a href="{{ route('trabalhos.index', request()->only(['estado', 'concluido'])) }}"
                       class="px-3 py-1.5 text-sm font-medium rounded-md bg-epoc-primary text-white hover:bg-epoc-primary-hover">
                        Lista
                    </a>
                    <a href="{{ route('trabalhos.index', array_merge(request()->only(['estado', 'concluido']), ['view' => 'kanban'])) }}"
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
                            <label for="estado" class="block text-xs font-medium text-gray-500 mb-0.5">Estado</label>
                            <select name="estado" id="estado" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                                <option value="">Todos</option>
                                <option value="em_espera" {{ request('estado') === 'em_espera' ? 'selected' : '' }}>Em espera</option>
                                <option value="em_execucao" {{ request('estado') === 'em_execucao' ? 'selected' : '' }}>Em execução</option>
                                <option value="pendente" {{ request('estado') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="concluido" {{ request('estado') === 'concluido' ? 'selected' : '' }}>Concluído</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">
                            Filtrar
                        </button>
                        @if (request()->has('estado') || request()->has('concluido'))
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
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prazo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gabinete</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tempo</th>
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
                                        @if ($item->servico?->tipo_trabalho)
                                            <span class="block text-xs text-gray-500">{{ $item->servico->tipo_trabalho }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $item->prazo_data?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $item->orcamento->gabinete?->nome ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <button type="button"
                                                class="text-epoc-primary hover:text-epoc-primary-hover underline-offset-2 hover:underline text-sm"
                                                data-trabalho-tecnico-url="{{ url(route('trabalhos.update-estado', $item)) }}"
                                                data-trabalho-tecnico-estado="{{ $item->estado }}">
                                            {{ $item->tecnico_nome ?? 'Definir técnico' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        {{ $item->tempo_total_formatado }}
                                        @if ($item->hasTempoAberto())
                                            <span class="inline-flex items-center ml-1">
                                                <span class="inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @switch($item->estado)
                                            @case('em_espera')
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-800">Em espera</span>
                                                @break
                                            @case('em_execucao')
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800">Em execução</span>
                                                @break
                                            @case('pendente')
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-amber-100 text-amber-800">Pendente</span>
                                                @if ($item->nota_pendente)
                                                    <span class="text-gray-500 text-xs block mt-0.5">{{ Str::limit($item->nota_pendente, 40) }}</span>
                                                @endif
                                                @break
                                            @case('concluido')
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-emerald-100 text-emerald-800">Concluído</span>
                                                @if ($item->concluido_em)
                                                    <span class="text-gray-400 text-xs block">{{ $item->concluido_em->format('d/m/Y H:i') }}</span>
                                                @endif
                                                @break
                                            @default
                                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-600">{{ $item->estado ?? '—' }}</span>
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        @php
                                            $targetEstado = null;
                                            $labelEstado = null;
                                            if ($item->estado === 'em_espera') {
                                                $targetEstado = 'em_execucao';
                                                $labelEstado = 'Iniciar';
                                            } elseif ($item->estado === 'em_execucao') {
                                                $targetEstado = 'pendente';
                                                $labelEstado = 'Pausar';
                                            } elseif ($item->estado === 'pendente') {
                                                $targetEstado = 'em_execucao';
                                                $labelEstado = 'Retomar';
                                            }
                                        @endphp

                                        @if ($targetEstado && auth()->user()->hasPermission('trabalhos.edit'))
                                            <button type="button"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white transition mr-2 {{ $targetEstado === 'pendente' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}"
                                                    title="{{ $labelEstado }}"
                                                    data-trabalho-estado-url="{{ route('trabalhos.update-estado', $item) }}"
                                                    data-trabalho-estado-target="{{ $targetEstado }}">
                                                @if ($targetEstado === 'pendente')
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 4a1 1 0 00-1 1v10a1 1 0 102 0V5a1 1 0 00-1-1zm6 0a1 1 0 00-1 1v10a1 1 0 102 0V5a1 1 0 00-1-1z"/></svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6.5 4.5a1 1 0 011.5-.866l7 4.5a1 1 0 010 1.732l-7 4.5A1 1 0 016 13.5v-9z"/></svg>
                                                @endif
                                            </button>
                                        @endif

                                        @if ($item->estado === 'concluido')
                                            <button type="button" class="trabalho-desfazer text-epoc-primary hover:text-epoc-primary-hover font-medium" data-url="{{ route('trabalhos.mark-concluido', $item) }}">Desfazer</button>
                                        @else
                                            @php
                                                $opts = $users->map(fn($u) => ['type' => 'user', 'id' => $u->id, 'name' => $u->name])->values()->all();
                                                if ($item->orcamento->subcontratado) {
                                                    $opts[] = ['type' => 'sub', 'id' => $item->orcamento->subcontratado->id, 'name' => $item->orcamento->subcontratado->nome . ' (subcontratado)'];
                                                }
                                                $temTecnico = $item->id_user || $item->id_subcontratado;
                                            @endphp
                                            <button type="button" class="trabalho-marcar-concluido inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white transition" title="Marcar concluído" data-url="{{ route('trabalhos.mark-concluido', $item) }}" data-tecnico-options="{{ json_encode($opts) }}" data-tem-tecnico="{{ $temTecnico ? '1' : '0' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
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

    {{-- Modal: erro ao alterar estado/trabalho --}}
    <div id="modal-erro-trabalho" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4" aria-modal="true" role="dialog" aria-labelledby="modal-erro-trabalho-title">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.518 11.59C19.021 16.92 18.262 18 17.105 18H2.895c-1.157 0-1.916-1.08-1.156-2.311l6.518-11.59zM11 8a1 1 0 10-2 0v4a1 1 0 102 0V8zm-1 8a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 16z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex-1">
                    <h3 id="modal-erro-trabalho-title" class="text-sm font-semibold text-gray-900">Aviso</h3>
                    <p id="modal-erro-trabalho-text" class="mt-1 text-sm text-gray-700"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" id="modal-erro-trabalho-ok" class="px-4 py-2 text-sm font-medium text-white bg-epoc-primary rounded-md hover:bg-epoc-primary-hover">
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
            let currentEstado = null;

            const modalErro = document.getElementById('modal-erro-trabalho');
            const modalErroText = document.getElementById('modal-erro-trabalho-text');
            const modalErroOk = document.getElementById('modal-erro-trabalho-ok');

            function openErrorModal(message) {
                if (!modalErro) return;
                modalErroText.textContent = message || 'Erro ao atualizar.';
                modalErro.classList.remove('hidden');
                modalErro.classList.add('flex');
            }

            function closeErrorModal() {
                if (!modalErro) return;
                modalErro.classList.add('hidden');
                modalErro.classList.remove('flex');
            }

            if (modalErroOk) {
                modalErroOk.addEventListener('click', closeErrorModal);
                modalErro.addEventListener('click', function(e) {
                    if (e.target === modalErro) closeErrorModal();
                });
            }

            function openModal(url, options, estadoForUpdate) {
                currentUrl = url;
                currentEstado = (estadoForUpdate !== undefined && estadoForUpdate !== '') ? estadoForUpdate : null;
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

            // Abrir modal de técnico a partir da lista (alterar técnico)
            document.querySelectorAll('[data-trabalho-tecnico-url]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.dataset.trabalhoTecnicoUrl;
                    const estado = this.dataset.trabalhoTecnicoEstado || '';
                    const row = this.closest('tr');
                    let optionsJson = '[]';
                    if (row) {
                        const conclBtn = row.querySelector('.trabalho-marcar-concluido');
                        if (conclBtn) {
                            optionsJson = conclBtn.dataset.tecnicoOptions || '[]';
                        }
                    }
                    openModal(url, JSON.parse(optionsJson), estado);
                });
            });

            document.querySelectorAll('.trabalho-marcar-concluido').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const url = this.dataset.url;
                    const temTecnico = this.dataset.temTecnico === '1';
                    if (temTecnico) {
                        this.disabled = true;
                        try {
                            const res = await fetch(url, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({})
                            });
                            const data = await res.json();
                            if (data.ok) {
                                if (data.orcamento_por_faturar) {
                                    document.getElementById('modal-por-faturar').classList.remove('hidden');
                                    document.getElementById('modal-por-faturar').classList.add('flex');
                                    return;
                                }
                                window.location.reload();
                            } else {
                                openErrorModal(data.message || 'Erro ao atualizar.');
                            }
                        } catch (e) {
                            openErrorModal('Erro de ligação ao servidor.');
                        }
                        this.disabled = false;
                    } else {
                        openModal(url, JSON.parse(this.dataset.tecnicoOptions || '[]'));
                    }
                });
            });

            confirmBtn.addEventListener('click', async function() {
                const val = select.value;
                if (!val || !currentUrl) return;
                const [type, id] = val.split('-');
                const body = type === 'user' ? { id_user: id } : { id_subcontratado: id };
                if (currentEstado) {
                    body.estado = currentEstado;
                }
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
                        openErrorModal(data.message || 'Erro ao atualizar.');
                    }
                } catch (e) {
                    openErrorModal('Erro de ligação ao servidor.');
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
                        if (data.ok) {
                            window.location.reload();
                        } else {
                            openErrorModal(data.message || 'Erro ao atualizar.');
                            this.disabled = false;
                        }
                    } catch (e) {
                        openErrorModal('Erro de ligação ao servidor.');
                        this.disabled = false;
                    }
                });
            });
        })();
    </script>
</x-app-layout>
