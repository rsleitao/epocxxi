<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Orçamento #{{ $orcamento->id }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $orcamento->designacao ?: 'Sem designação' }}
                </p>
            </div>
            @if (empty($exportMode))
                <div class="flex items-center gap-3">
                    <button type="button"
                            onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                        Imprimir / Guardar PDF
                    </button>
                    <a href="{{ route('orcamentos.edit', $orcamento) }}"
                       class="inline-flex items-center px-4 py-2 bg-epoc-primary text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-epoc-primary-hover">
                        Voltar ao orçamento
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8 print:py-0">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg print:shadow-none print:rounded-none">
                <div class="p-6 space-y-8 print:p-8">
                    {{-- Cabeçalho / identificação --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6 border-b border-gray-200 pb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Proposta para</h3>
                            <dl class="mt-3 space-y-1 text-sm">
                                <div>
                                    <dt class="text-gray-500">Requerente</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ $orcamento->requerente?->nome ?? '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Faturar a</dt>
                                    <dd class="font-medium text-gray-900">
                                        {{ $orcamento->requerenteFatura?->nome ?? $orcamento->requerente?->nome ?? '—' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div class="text-sm text-right space-y-1">
                            <p class="text-gray-500 mt-3">Data</p>
                            <p class="font-medium text-gray-900">{{ $orcamento->created_at?->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Imóvel --}}
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Imóvel</h3>
                        @if (! $orcamento->imovel)
                            <p class="text-sm text-gray-500">Nenhum imóvel associado a este orçamento.</p>
                        @else
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                                <div>
                                    <dt class="text-gray-500">Tipo</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->tipoImovel?->tipo_imovel ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Morada</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->morada ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">NIP</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->nip ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Código postal</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->codigo_postal ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Localidade</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->localidade ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Coordenadas</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->coordenadas ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Distrito</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->distrito?->nome ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Concelho</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->concelho?->nome ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Freguesia</dt>
                                    <dd class="font-medium text-gray-900">{{ $orcamento->imovel->freguesia?->nome ?? '—' }}</dd>
                                </div>
                            </dl>
                        @endif
                    </div>

                    {{-- Linhas + totais --}}
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Proposta económica</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md table-fixed text-sm">
                                <colgroup>
                                    <col class="w-40">
                                    <col>
                                    <col class="w-28">
                                    <col class="w-28">
                                    <col class="w-24">
                                    <col class="w-24">
                                    <col class="w-20">
                                    <col class="w-24">
                                    <col class="w-28">
                                </colgroup>
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fase</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prazo</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qtd</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Preço base</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">IVA %</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Valor IVA</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $subtotal = 0;
                                        $totalIva = 0;
                                        $ivaOrcamento = (float) ($orcamento->percentagem_iva ?? 23);
                                    @endphp
                                    @forelse ($orcamento->itens as $item)
                                        @php
                                            $valorLinha = (float) $item->preco_base * (float) ($item->quantidade ?? 1);
                        $ivaPctLinha = (float) ($item->percentagem_iva ?? $ivaOrcamento);
                                            $valorIvaLinha = round($valorLinha * ($ivaPctLinha / 100), 2);
                                            $totalLinha = $valorLinha + $valorIvaLinha;
                                            $subtotal += $valorLinha;
                                            $totalIva += $valorIvaLinha;
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900">{{ $item->servico?->nome ?? 'Serviço ocasional' }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ $item->descricao ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $item->servico?->tipo_trabalho ?? '—' }}</td>
                                            <td class="px-3 py-2 text-gray-700">{{ $item->prazo_data?->format('d/m/Y') ?? '—' }}</td>
                                            <td class="px-3 py-2 text-right text-gray-900">{{ $item->quantidade ?? '—' }}</td>
                                            <td class="px-3 py-2 text-right text-gray-900">{{ number_format((float) $item->preco_base, 2, ',', ' ') }} €</td>
                                            <td class="px-3 py-2 text-right text-gray-600">{{ number_format($ivaPctLinha, 0) }}%</td>
                                            <td class="px-3 py-2 text-right text-gray-900">{{ number_format($valorIvaLinha, 2, ',', ' ') }} €</td>
                                            <td class="px-3 py-2 text-right font-medium text-gray-900">{{ number_format($totalLinha, 2, ',', ' ') }} €</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-3 py-4 text-center text-gray-500">Nenhuma linha de orçamento.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($orcamento->itens->isNotEmpty())
                                    <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-medium">
                                        <tr>
                                            <td colspan="5" class="px-3 py-3"></td>
                                            <td class="px-3 py-3 text-right text-gray-900">{{ number_format($subtotal, 2, ',', ' ') }} €</td>
                                            <td class="px-3 py-3 text-right text-gray-600">{{ number_format($ivaOrcamento, 0) }}%</td>
                                            <td class="px-3 py-3 text-right text-gray-900">{{ number_format($totalIva, 2, ',', ' ') }} €</td>
                                            <td class="px-3 py-3 text-right text-gray-900">{{ number_format($subtotal + $totalIva, 2, ',', ' ') }} €</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if (empty($exportMode))
                        {{-- Observações internas (não incluídas nos documentos exportados) --}}
                        <div class="pt-4 border-t border-dashed border-gray-200 print:border-none print:pt-8">
                            <p class="text-xs text-gray-500">
                                Este documento foi gerado automaticamente pelo sistema. Confirme sempre os valores antes de o enviar ao cliente.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

