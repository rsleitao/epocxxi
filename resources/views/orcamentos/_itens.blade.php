@props(['readonly' => false])
@php
    $servicos = $servicos ?? collect();
    $percentagemIva = old('percentagem_iva', $orcamento?->percentagem_iva ?? 23);
    $itensIniciais = isset($orcamento) && $orcamento && $orcamento->itens->isNotEmpty()
        ? $orcamento->itens->map(fn ($i) => [
            'id_servico' => $i->id_servico ? (string) $i->id_servico : '',
            'descricao' => $i->descricao,
            'preco_base' => $i->preco_base !== null ? (string) $i->preco_base : '',
            'quantidade' => $i->quantidade !== null ? (string) $i->quantidade : '1',
            'prazo_data' => $i->prazo_data?->format('Y-m-d') ?? '',
            'percentagem_iva' => $i->percentagem_iva !== null ? (string) $i->percentagem_iva : '',
        ])->values()->all()
        : [['id_servico' => '', 'descricao' => '', 'preco_base' => '', 'quantidade' => '1', 'prazo_data' => '', 'percentagem_iva' => '']];
    $servicosJson = $servicos->map(fn ($s) => ['id' => $s->id, 'nome' => $s->nome, 'descricao' => $s->descricao ?? '', 'preco_base' => (float) $s->preco_base, 'unidade' => $s->unidade ?? '', 'tipo_trabalho' => $s->tipo_trabalho ?? ''])->values()->all();
@endphp
@if ($readonly)
<div class="border-t border-gray-200 pt-6 mt-6">
    <h3 class="text-sm font-medium text-gray-700 mb-3">Linhas do orçamento</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md table-fixed">
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
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantidade</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Preço base</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">IVA %</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Valor IVA</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $subtotalReadonly = 0;
                    $valorIvaTotalReadonly = 0;
                @endphp
                @forelse ($orcamento->itens as $item)
                @php
                    $valorLinha = (float) $item->preco_base * (float) ($item->quantidade ?? 1);
                    $ivaPctLinha = (float) ($item->percentagem_iva ?? $orcamento->percentagem_iva ?? 23);
                    $valorIvaLinha = round($valorLinha * ($ivaPctLinha / 100), 2);
                    $totalLinha = $valorLinha + $valorIvaLinha;
                    $subtotalReadonly += $valorLinha;
                    $valorIvaTotalReadonly += $valorIvaLinha;
                @endphp
                <tr>
                    <td class="px-3 py-2 text-sm text-gray-900">{{ $item->servico?->nome ?? 'Serviço ocasional' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ $item->descricao ?? '—' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-600">{{ $item->servico?->tipo_trabalho ?? '—' }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ $item->prazo_data?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-3 py-2 text-sm text-right text-gray-900">{{ $item->quantidade ?? '—' }}</td>
                    <td class="px-3 py-2 text-sm text-right text-gray-900">{{ number_format((float) $item->preco_base, 2, ',', ' ') }} €</td>
                    <td class="px-3 py-2 text-sm text-right text-gray-600">{{ number_format($ivaPctLinha, 0) }}%</td>
                    <td class="px-3 py-2 text-sm text-right text-gray-900">{{ number_format($valorIvaLinha, 2, ',', ' ') }} €</td>
                    <td class="px-3 py-2 text-sm text-right font-medium text-gray-900">{{ number_format($totalLinha, 2, ',', ' ') }} €</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-3 py-4 text-sm text-gray-500 text-center">Nenhuma linha.</td>
                </tr>
                @endforelse
            </tbody>
            @if ($orcamento->itens->isNotEmpty())
            <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-medium text-sm">
                <tr>
                    <td class="px-3 py-3" colspan="5"></td>
                    <td class="px-3 py-3 text-right text-gray-900">{{ number_format($subtotalReadonly, 2, ',', ' ') }} €</td>
                    <td class="px-3 py-3 text-right text-gray-600">{{ number_format((float) ($orcamento->percentagem_iva ?? 23), 0) }}%</td>
                    <td class="px-3 py-3 text-right text-gray-900">{{ number_format($valorIvaTotalReadonly, 2, ',', ' ') }} €</td>
                    <td class="px-3 py-3 text-right text-gray-900">{{ number_format($subtotalReadonly + $valorIvaTotalReadonly, 2, ',', ' ') }} €</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@else
<div class="border-t border-gray-200 pt-6 mt-6" x-data="{
    itens: @js($itensIniciais),
    percentagem_iva: @js((float) $percentagemIva),
    servicos: @js($servicosJson),
    valorLinha(item) {
        return (parseFloat(item.preco_base)||0) * (parseFloat(item.quantidade)||1);
    },
    ivaPctLinha(item) {
        const p = item.percentagem_iva !== '' && item.percentagem_iva != null ? parseFloat(item.percentagem_iva) : null;
        return p != null && !isNaN(p) ? p : this.percentagem_iva;
    },
    valorIvaLinha(item) {
        const v = this.valorLinha(item);
        const p = this.ivaPctLinha(item);
        return Math.round(v * (p/100) * 100) / 100;
    },
    totalLinha(item) {
        return this.valorLinha(item) + this.valorIvaLinha(item);
    },
    get subtotal() {
        return this.itens.reduce((acc, i) => acc + this.valorLinha(i), 0);
    },
    get valorIva() {
        return this.itens.reduce((acc, i) => acc + this.valorIvaLinha(i), 0);
    },
    get total() {
        return this.subtotal + this.valorIva;
    },
    escolherServico(item, idServico) {
        if (!idServico) return;
        const s = this.servicos.find(x => x.id == idServico);
        if (s) {
            item.descricao = s.descricao ? (s.nome + ' - ' + s.descricao) : s.nome;
            item.preco_base = s.preco_base;
        }
    },
    novaLinha() {
        return { id_servico: '', descricao: '', preco_base: '', quantidade: '1', prazo_data: '', percentagem_iva: '' };
    },
    fmt(n) { return (typeof n === 'number' ? n.toFixed(2) : '0,00').replace('.', ','); }
}">
    <div class="flex justify-between items-center mb-3">
        <h3 class="text-sm font-medium text-gray-700">Linhas do orçamento</h3>
        <p class="text-xs text-gray-500">Escolha um serviço para preencher automaticamente ou deixe "Serviço ocasional". IVA % em branco usa o valor do orçamento ({{ number_format($percentagemIva, 0) }}%).</p>
        <button type="button" @click="itens.push(novaLinha())"
                class="text-sm text-epoc-primary hover:text-epoc-primary-hover font-medium">
            + Adicionar linha
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-md table-fixed">
            <colgroup>
                <col class="w-40">
                <col class="w-28">
                <col class="w-28">
                <col class="w-24">
                <col class="w-32">
                <col class="w-24">
                <col class="w-24">
                <col class="w-28">
                <col class="w-8">
            </colgroup>
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fase</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prazo</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantidade</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Preço base</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">IVA %</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Valor IVA</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-8"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" x-cloak>
                <template x-for="(item, index) in itens" :key="index">
                    <tr>
                        <td class="px-3 py-2">
                            <input type="hidden" :name="'itens[' + index + '][descricao]'" x-model="item.descricao">
                            <select :name="'itens[' + index + '][id_servico]'" x-model="item.id_servico"
                                    @change="escolherServico(item, item.id_servico)"
                                    class="block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm text-sm">
                                <option value="">— Serviço ocasional —</option>
                                @foreach ($servicos as $s)
                                    <option value="{{ $s->id }}">{{ $s->nome }} @if($s->unidade)({{ $s->unidade }})@endif</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-600 align-middle" x-text="(servicos.find(s => s.id == item.id_servico) || {}).tipo_trabalho || '—'"></td>
                        <td class="px-3 py-2">
                            <input type="date" :name="'itens[' + index + '][prazo_data]'" x-model="item.prazo_data"
                                   class="block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm text-sm">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="0.01" min="0" :name="'itens[' + index + '][quantidade]'" x-model="item.quantidade"
                                   class="block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm text-sm text-right">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="0.01" min="0" :name="'itens[' + index + '][preco_base]'" x-model="item.preco_base"
                                   class="block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm text-sm text-right">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" step="0.01" min="0" max="100" :name="'itens[' + index + '][percentagem_iva]'" x-model="item.percentagem_iva"
                                   class="block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm text-sm text-right" placeholder="{{ number_format($percentagemIva, 0) }}">
                        </td>
                        <td class="px-3 py-2 text-sm text-right text-gray-900 align-middle" x-text="fmt(valorIvaLinha(item)) + ' €'"></td>
                        <td class="px-3 py-2 text-sm text-right font-medium text-gray-900 align-middle" x-text="fmt(totalLinha(item)) + ' €'"></td>
                        <td class="px-1 py-2 text-center">
                            <button type="button" @click="itens.splice(index, 1)" class="text-red-600 hover:text-red-800 text-2xl font-bold leading-none align-middle" title="Remover linha">×</button>
                        </td>
                    </tr>
                </template>
            </tbody>
            <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-medium text-sm">
                <tr>
                    <td class="px-3 py-3" colspan="4"></td>
                    <td class="px-3 py-3 text-right text-gray-900" x-text="fmt(subtotal) + ' €'"></td>
                    <td class="px-3 py-3 text-right">
                        <input type="number" name="percentagem_iva" x-model.number="percentagem_iva" step="0.01" min="0" max="100"
                               class="w-full max-w-[4rem] border-0 bg-transparent p-0 text-right text-gray-700 focus:ring-0" title="IVA % (geral)">
                    </td>
                    <td class="px-3 py-3 text-right text-gray-900" x-text="fmt(valorIva) + ' €'"></td>
                    <td class="px-3 py-3 text-right text-gray-900" x-text="fmt(total) + ' €'"></td>
                    <td class="px-3 py-3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endif
