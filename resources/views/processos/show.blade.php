<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('processos.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Processos</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Processo {{ $processo->referencia }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
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
                                            <a href="{{ route('orcamentos.edit', $orcamento) }}" class="text-epoc-primary hover:text-epoc-primary-hover">{{ in_array($orcamento->status, ['aceite', 'em_execucao', 'por_faturar', 'faturado']) ? 'Ver' : 'Editar' }}</a>
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
    </div>
</x-app-layout>
