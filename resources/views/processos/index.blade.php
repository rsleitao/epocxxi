<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Processos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 space-y-3">
                    <form method="get" action="{{ route('processos.index') }}" class="flex flex-wrap items-end gap-2">
                        <div>
                            <label for="ano" class="block text-xs font-medium text-gray-500 mb-0.5">Ano</label>
                            <select name="ano" id="ano" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm w-24">
                                <option value="">Todos</option>
                                @foreach ($anos as $a)
                                    <option value="{{ $a }}" {{ request('ano') == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="q" class="block text-xs font-medium text-gray-500 mb-0.5">Pesquisar</label>
                            <input type="search" name="q" id="q" value="{{ request('q') }}"
                                   placeholder="Ref., designação, requerente ou imóvel..."
                                   class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary w-64 text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">
                            Filtrar
                        </button>
                        @if (request()->hasAny(['ano', 'q']))
                            <a href="{{ route('processos.index') }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">
                                Limpar
                            </a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Referência</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Designação</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requerente</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Imóvel</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Orçamentos</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($processos as $processo)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ $processo->referencia ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($processo->designacao, 35) ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $processo->requerente?->nome ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($processo->imovel?->morada, 30) ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $processo->orcamentos_count }}</td>
                                    <td class="px-4 py-3 text-sm text-right">
                                        <a href="{{ route('processos.show', $processo) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum processo encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($processos->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $processos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
