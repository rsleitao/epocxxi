<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Relatórios
                </h2>
                <p class="mt-1 text-sm text-gray-600 max-w-2xl">
                    Visão geral dos trabalhos concluídos: tempo e produtividade por gabinete. Use os filtros para definir o período e o âmbito.
                </p>
            </div>
            <a href="{{ route('gestao.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Voltar à Gestão</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Trabalhos concluídos</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalTrabalhos, 0, ',', ' ') }}</p>
                    <p class="mt-0.5 text-sm text-gray-500">No período e âmbito selecionados</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tempo total</p>
                    <p class="mt-1 text-2xl font-bold text-epoc-primary">{{ $totalTempoFormatado }}</p>
                    <p class="mt-0.5 text-sm text-gray-500">Soma do tempo registado nos trabalhos concluídos</p>
                </div>
            </div>

            {{-- Tabela por gabinete --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">Por gabinete</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Número de trabalhos concluídos e tempo total por gabinete responsável pelo orçamento.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gabinete</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Trabalhos</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Tempo total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($porGabinete as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['gabinete_nome'] }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-700">{{ number_format($row['count'], 0, ',', ' ') }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-700">{{ $row['tempo_formatado'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                        Nenhum trabalho concluído no período e âmbito selecionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
