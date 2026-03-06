<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Relatórios
                </h2>
                <p class="mt-1 text-sm text-gray-600 max-w-2xl">
                    Estatísticas e gráficos dos trabalhos concluídos: por gabinete, técnico, tipo de serviço e evolução mensal. Use os filtros para definir o período.
                </p>
            </div>
            <a href="{{ route('gestao.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Voltar à Gestão</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Definições: custo horário (editável pelo admin/CEO) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Definições dos relatórios</h3>
                <form method="post" action="{{ route('gestao.relatorios.custo_hora') }}" class="flex flex-wrap items-end gap-4">
                    @csrf
                    <div>
                        <label for="custo_hora" class="block text-xs font-medium text-gray-600 mb-1">Custo horário médio (€/h)</label>
                        <input type="number" id="custo_hora" name="custo_hora" value="{{ old('custo_hora', $custoHora) }}"
                               step="0.01" min="0" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm w-24"
                               title="Usado para calcular o custo baseado no tempo e ver se compensa face ao preço cobrado.">
                        <p class="mt-0.5 text-xs text-gray-500">Usado para custo estimado e análise «compensa».</p>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-md text-sm font-medium hover:bg-gray-600">
                        Guardar
                    </button>
                </form>
            </div>

            {{-- Filtros --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Filtros</h3>
                <form method="get" action="{{ route('gestao.relatorios') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="data_inicio" class="block text-xs font-medium text-gray-600 mb-1">Data início</label>
                        <input type="date" id="data_inicio" name="data_inicio" value="{{ $filtros['data_inicio'] ?? '' }}"
                               class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                    </div>
                    <div>
                        <label for="data_fim" class="block text-xs font-medium text-gray-600 mb-1">Data fim</label>
                        <input type="date" id="data_fim" name="data_fim" value="{{ $filtros['data_fim'] ?? '' }}"
                               class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                    </div>
                    <div>
                        <label for="id_gabinete" class="block text-xs font-medium text-gray-600 mb-1">Gabinete</label>
                        <select id="id_gabinete" name="id_gabinete" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm min-w-[180px]">
                            <option value="">— Todos —</option>
                            @foreach ($gabinetes as $g)
                                <option value="{{ $g->id }}" {{ ($filtros['id_gabinete'] ?? '') == $g->id ? 'selected' : '' }}>{{ $g->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="id_user" class="block text-xs font-medium text-gray-600 mb-1">Técnico</label>
                        <select id="id_user" name="id_user" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm min-w-[180px]">
                            <option value="">— Todos —</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ ($filtros['id_user'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-epoc-primary text-white rounded-md text-sm font-medium hover:bg-epoc-primary-hover">
                            Aplicar
                        </button>
                        <a href="{{ route('gestao.relatorios') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-50">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Trabalhos concluídos</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalTrabalhos, 0, ',', ' ') }}</p>
                    <p class="mt-0.5 text-sm text-gray-500">No período e âmbito selecionados</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tempo total</p>
                    <p class="mt-1 text-2xl font-bold text-epoc-primary">{{ $totalTempoFormatado }}</p>
                    <p class="mt-0.5 text-sm text-gray-500">Soma do tempo registado</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Faturação</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ number_format($totalFaturado, 2, ',', ' ') }} €</p>
                    <p class="mt-0.5 text-sm text-gray-500">Soma preço × quantidade (período)</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Por faturar</p>
                    <p class="mt-1 text-2xl font-bold text-red-600">{{ number_format($totalPorFaturar, 2, ',', ' ') }} €</p>
                    <p class="mt-0.5 text-sm text-gray-500">Orçamentos em estado «Por faturar»</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Custo do tempo (total)</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        {{ number_format($totalCustoTempo, 2, ',', ' ') }} €
                    </p>
                    <p class="mt-0.5 text-sm text-gray-500">
                        Total de horas gastas × {{ number_format($custoHora, 0, ',', ' ') }} €/h (cada hora custa {{ number_format($custoHora, 0, ',', ' ') }} €). Margem: {{ number_format($margem, 2, ',', ' ') }} €.
                    </p>
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Trabalhos por gabinete</h3>
                    <div class="h-64">
                        <canvas id="chart-gabinete" height="256"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Evolução mensal (faturação)</h3>
                    <div class="h-64">
                        <canvas id="chart-mensal" height="256"></canvas>
                    </div>
                </div>
            </div>

            {{-- Tabelas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Por gabinete --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800">Por gabinete</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Trabalhos e tempo total por gabinete.</p>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gabinete</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Trabalhos</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tempo</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Faturado</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Custo tempo</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Compensa</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($porGabinete as $row)
                                    <tr>
                                        <td class="px-3 py-3 text-sm font-medium text-gray-900">{{ $row['gabinete_nome'] }}</td>
                                        <td class="px-3 py-3 text-sm text-right text-gray-700">{{ number_format($row['count'], 0, ',', ' ') }}</td>
                                        <td class="px-3 py-3 text-sm text-right font-mono text-gray-700">{{ $row['tempo_formatado'] }}</td>
                                        <td class="px-3 py-3 text-sm text-right text-gray-700">{{ number_format($row['faturado'], 2, ',', ' ') }} €</td>
                                        <td class="px-3 py-3 text-sm text-right text-gray-700">{{ number_format($row['custoTempo'], 2, ',', ' ') }} €</td>
                                        <td class="px-3 py-3 text-sm text-center">
                                            @if ($row['compensa'])
                                                <span class="text-green-600 font-medium">Sim</span>
                                            @else
                                                <span class="text-red-600 font-medium">Não</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Nenhum dado no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Por técnico --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800">Por técnico</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Trabalhos e tempo total por técnico.</p>
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Trabalhos</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tempo</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Faturado</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Custo tempo</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Compensa</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($porTecnico as $row)
                                    <tr>
                                        <td class="px-3 py-3 text-sm font-medium text-gray-900">{{ $row['tecnico_nome'] }}</td>
                                        <td class="px-3 py-3 text-sm text-right text-gray-700">{{ number_format($row['count'], 0, ',', ' ') }}</td>
                                        <td class="px-3 py-3 text-sm text-right font-mono text-gray-700">{{ $row['tempo_formatado'] }}</td>
                                        <td class="px-3 py-3 text-sm text-right text-gray-700">{{ number_format($row['faturado'], 2, ',', ' ') }} €</td>
                                        <td class="px-3 py-3 text-sm text-right text-gray-700">{{ number_format($row['custoTempo'], 2, ',', ' ') }} €</td>
                                        <td class="px-3 py-3 text-sm text-center">
                                            @if ($row['compensa'])
                                                <span class="text-green-600 font-medium">Sim</span>
                                            @else
                                                <span class="text-red-600 font-medium">Não</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Nenhum dado no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Por tipo de imóvel (onde se ganha mais) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">Por tipo de imóvel</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Onde se ganha mais: faturação e tempo por tipo de imóvel do orçamento.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo de imóvel</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Trabalhos</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tempo</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Faturado</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Custo tempo</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Compensa</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($porTipoImovel as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['tipo_imovel_nome'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($row['count'], 0, ',', ' ') }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-700">{{ $row['tempo_formatado'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($row['faturado'], 2, ',', ' ') }} €</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($row['custoTempo'], 2, ',', ' ') }} €</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        @if ($row['compensa'])
                                            <span class="text-green-600 font-medium">Sim</span>
                                        @else
                                            <span class="text-red-600 font-medium">Não</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Nenhum dado no período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Por tipo de serviço --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">Por tipo de serviço</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Trabalhos e tempo total por serviço (nome e tipo).</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Trabalhos</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tempo</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Faturado</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Custo tempo</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Compensa</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($porServico as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['servico_nome'] }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $row['tipo_trabalho'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($row['count'], 0, ',', ' ') }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-700">{{ $row['tempo_formatado'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($row['faturado'], 2, ',', ' ') }} €</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($row['custoTempo'], 2, ',', ' ') }} €</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        @if ($row['compensa'])
                                            <span class="text-green-600 font-medium">Sim</span>
                                        @else
                                            <span class="text-red-600 font-medium">Não</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Nenhum dado no período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="relatorios-por-gabinete">{!! json_encode($porGabinete) !!}</script>
    <script type="application/json" id="relatorios-por-mes">{!! json_encode($porMes) !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            var porGabinete = [];
            var porMes = [];
            try {
                var elGab = document.getElementById('relatorios-por-gabinete');
                var elMes = document.getElementById('relatorios-por-mes');
                if (elGab && elGab.textContent) porGabinete = JSON.parse(elGab.textContent);
                if (elMes && elMes.textContent) porMes = JSON.parse(elMes.textContent);
            } catch (e) {}

            if (porGabinete.length > 0) {
                new Chart(document.getElementById('chart-gabinete'), {
                    type: 'bar',
                    data: {
                        labels: porGabinete.map(function(r) { return r.gabinete_nome; }),
                        datasets: [{
                            label: 'Trabalhos concluídos',
                            data: porGabinete.map(function(r) { return r.count; }),
                            backgroundColor: 'rgba(59, 130, 246, 0.6)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
            } else {
                document.getElementById('chart-gabinete').parentNode.innerHTML = '<p class="text-sm text-gray-500 flex items-center justify-center h-full">Sem dados para o período.</p>';
            }

            if (porMes.length > 0) {
                new Chart(document.getElementById('chart-mensal'), {
                    type: 'line',
                    data: {
                        labels: porMes.map(function(r) { return r.label; }),
                        datasets: [{
                            label: 'Faturação (€)',
                            data: porMes.map(function(r) { return r.faturado != null ? r.faturado : 0; }),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            fill: true,
                            tension: 0.2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: function(v) { return v + ' €'; } } }
                        }
                    }
                });
            } else {
                document.getElementById('chart-mensal').parentNode.innerHTML = '<p class="text-sm text-gray-500 flex items-center justify-center h-full">Sem dados para o período.</p>';
            }
        })();
    </script>
</x-app-layout>
