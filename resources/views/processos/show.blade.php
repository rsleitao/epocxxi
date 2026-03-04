<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('processos.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Processos</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Processo {{ $processo->referencia }}
                </h2>
            </div>
            @if (!empty($hasTemplatesPartesEscritas) && $hasTemplatesPartesEscritas)
                <button type="button"
                        x-data
                        @click="$dispatch('abrir-modal-partes-escritas')"
                        class="inline-flex items-center px-3 py-1.5 rounded-md bg-epoc-primary text-white text-xs font-semibold hover:bg-epoc-primary-hover">
                    Imprimir Partes Escritas
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-12"
         x-data="{
            modalDocs: false,
            selectedTemplates: [],
            templatesWord: @js($templatesPartesEscritasWord ?? []),
            templatesExcel: @js($templatesPartesEscritasExcel ?? []),
            toggleAll(e) {
                if (e.target.checked) {
                    this.selectedTemplates = [...this.templatesWord, ...this.templatesExcel].map(t => t.id);
                } else {
                    this.selectedTemplates = [];
                }
            }
         }"
         x-on:abrir-modal-partes-escritas.window="modalDocs = true">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Dados do processo --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 font-medium text-gray-700">Dados do processo</div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Designação</span>
                        <p class="text-gray-900">{{ $processo->designacao ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Data de criação</span>
                        <p class="text-gray-900">{{ $processo->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Requerente</span>
                        <p class="text-gray-900">
                            @if ($processo->requerente)
                                <a href="{{ route('requerentes.show', $processo->requerente) }}" class="text-epoc-primary hover:underline">{{ $processo->requerente->nome }}</a>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-500">Valor faturado (neste processo)</span>
                        <p class="text-gray-900 font-medium">{{ number_format($valorFaturado, 2, ',', ' ') }} €</p>
                        <p class="text-gray-500 text-xs">Soma dos orçamentos com estado Faturado</p>
                    </div>
                </div>
            </div>

            {{-- Imóvel --}}
            @if ($processo->imovel)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 font-medium text-gray-700">Imóvel</div>
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Morada</span>
                        <p class="text-gray-900">{{ $processo->imovel->morada ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">NIP</span>
                        <p class="text-gray-900">{{ $processo->imovel->nip ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Tipo</span>
                        <p class="text-gray-900">{{ $processo->imovel->tipoImovel?->tipo_imovel ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Localidade</span>
                        <p class="text-gray-900">{{ $processo->imovel->localidade ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Código postal</span>
                        <p class="text-gray-900">{{ $processo->imovel->codigo_postal ?? '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Distrito / Concelho / Freguesia</span>
                        <p class="text-gray-900">
                            {{ trim(implode(' / ', array_filter([
                                $processo->imovel->distrito?->nome,
                                $processo->imovel->concelho?->nome,
                                $processo->imovel->freguesia?->nome,
                            ]))) ?: '—' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Orçamentos do processo --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 font-medium text-gray-700">Orçamentos ({{ $processo->orcamentos->count() }})</div>
                <div class="overflow-x-auto">
                    @if ($processo->orcamentos->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nº</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Designação</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gabinete</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subcontratado</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($processo->orcamentos as $orcamento)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-500">{{ $orcamento->numero ?? $orcamento->id }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $badges = [
                                                    'rascunho' => 'bg-gray-100 text-gray-800',
                                                    'enviado' => 'bg-blue-100 text-blue-800',
                                                    'aceite' => 'bg-green-100 text-green-800',
                                                    'recusado' => 'bg-red-100 text-red-800',
                                                    'cancelado' => 'bg-gray-200 text-gray-700',
                                                    'em_execucao' => 'bg-epoc-lighter text-epoc-primary',
                                                    'por_faturar' => 'bg-amber-100 text-amber-800',
                                                    'faturado' => 'bg-emerald-100 text-emerald-800',
                                                ];
                                                $statusLabels = [
                                                    'rascunho' => 'Rascunho', 'enviado' => 'Enviado', 'aceite' => 'Aceite',
                                                    'recusado' => 'Recusado', 'cancelado' => 'Cancelado', 'em_execucao' => 'Em execução',
                                                    'por_faturar' => 'Por faturar', 'faturado' => 'Faturado',
                                                ];
                                                $c = $badges[$orcamento->status] ?? 'bg-gray-100 text-gray-800';
                                                $label = $statusLabels[$orcamento->status] ?? $orcamento->status;
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded {{ $c }}">{{ $label }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($orcamento->designacao, 40) ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $orcamento->gabinete?->nome ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $orcamento->subcontratado?->nome ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-right space-x-2">
                                            <a href="{{ route('orcamentos.edit', $orcamento) }}" class="text-epoc-primary hover:text-epoc-primary-hover">{{ in_array($orcamento->status, ['enviado', 'aceite', 'em_execucao', 'por_faturar', 'faturado']) ? 'Ver' : 'Editar' }}</a>
                                            @if (in_array($orcamento->status, ['aceite', 'em_execucao', 'por_faturar', 'faturado']))
                                                <a href="{{ route('orcamentos.report', $orcamento) }}" target="_blank" class="text-gray-600 hover:text-gray-900">Imprimir</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-6 text-center text-gray-500">
                            Nenhum orçamento associado a este processo.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal: documentos Partes Escritas --}}
        <div x-show="modalDocs"
             x-cloak
             x-transition
             @keydown.escape.window="modalDocs = false"
             @click.self="modalDocs = false"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-xl w-full max-h-[80vh] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Imprimir documentos – Partes Escritas</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="modalDocs = false">&times;</button>
                </div>
                <form method="post" action="{{ route('processos.documentos.parteescritas', $processo) }}" class="flex-1 flex flex-col">
                    @csrf
                    <div class="p-4 space-y-4 overflow-y-auto">
                        <template x-if="templatesWord.length === 0 && templatesExcel.length === 0">
                            <p class="text-sm text-gray-500">Não existem templates configurados para Partes Escritas.</p>
                        </template>
                        <template x-if="templatesWord.length > 0 || templatesExcel.length > 0">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>Selecione um ou mais documentos para gerar.</span>
                                    <label class="inline-flex items-center gap-1 cursor-pointer">
                                        <input type="checkbox" @change="toggleAll" class="rounded border-gray-300 text-epoc-primary shadow-sm focus:ring-epoc-primary">
                                        <span>Selecionar todos</span>
                                    </label>
                                </div>
                                <div class="border border-gray-100 rounded-md">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
                                        <div class="p-3 space-y-1">
                                            <h4 class="text-xs font-semibold text-gray-500 uppercase">Word</h4>
                                            <template x-if="templatesWord.length === 0">
                                                <p class="text-xs text-gray-400 mt-1">Sem templates Word.</p>
                                            </template>
                                            <template x-for="tpl in templatesWord" :key="'w-' + tpl.id">
                                                <label class="flex items-center justify-between gap-3 py-1.5 text-sm cursor-pointer hover:bg-gray-50 rounded">
                                                    <div class="flex items-center gap-2">
                                                        <input type="checkbox"
                                                               name="templates[]"
                                                               :value="tpl.id"
                                                               class="rounded border-gray-300 text-epoc-primary shadow-sm focus:ring-epoc-primary"
                                                               x-model="selectedTemplates">
                                                        <div>
                                                            <p class="font-medium text-gray-900" x-text="tpl.nome"></p>
                                                            <p class="text-xs text-gray-500" x-text="tpl.nome_original || tpl.ficheiro"></p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                        <div class="p-3 space-y-1">
                                            <h4 class="text-xs font-semibold text-gray-500 uppercase">Excel</h4>
                                            <template x-if="templatesExcel.length === 0">
                                                <p class="text-xs text-gray-400 mt-1">Sem templates Excel.</p>
                                            </template>
                                            <template x-for="tpl in templatesExcel" :key="'e-' + tpl.id">
                                                <label class="flex items-center justify-between gap-3 py-1.5 text-sm cursor-pointer hover:bg-gray-50 rounded">
                                                    <div class="flex items-center gap-2">
                                                        <input type="checkbox"
                                                               name="templates[]"
                                                               :value="tpl.id"
                                                               class="rounded border-gray-300 text-epoc-primary shadow-sm focus:ring-epoc-primary"
                                                               x-model="selectedTemplates">
                                                        <div>
                                                            <p class="font-medium text-gray-900" x-text="tpl.nome"></p>
                                                            <p class="text-xs text-gray-500" x-text="tpl.nome_original || tpl.ficheiro"></p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="p-4 border-t border-gray-200 flex items-center justify-between gap-3">
                        <p class="text-xs text-gray-500" x-show="selectedTemplates.length > 0" x-text="selectedTemplates.length + ' selecionado(s)'"></p>
                        <div class="flex items-center gap-3 ml-auto">
                            <button type="button" class="text-sm text-gray-600 hover:text-gray-900" @click="modalDocs = false">Cancelar</button>
                            <x-primary-button type="submit" x-bind:disabled="selectedTemplates.length === 0">
                                Gerar ZIP
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
