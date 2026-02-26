<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Orçamentos
            </h2>
            <a href="{{ route('orcamentos.create') }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo orçamento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 space-y-3">
                    <form method="get" action="{{ route('orcamentos.index') }}" class="flex flex-wrap items-end gap-2">
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-500 mb-0.5">Estado</label>
                            <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                                <option value="">Todos</option>
                                <option value="rascunho" {{ request('status') === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                                <option value="enviado" {{ request('status') === 'enviado' ? 'selected' : '' }}>Enviado</option>
                                <option value="aceite" {{ request('status') === 'aceite' ? 'selected' : '' }}>Aceite</option>
                                <option value="recusado" {{ request('status') === 'recusado' ? 'selected' : '' }}>Recusado</option>
                                <option value="convertido" {{ request('status') === 'convertido' ? 'selected' : '' }}>Convertido</option>
                                <option value="faturado" {{ request('status') === 'faturado' ? 'selected' : '' }}>Faturado</option>
                            </select>
                        </div>
                        <div>
                            <label for="id_gabinete" class="block text-xs font-medium text-gray-500 mb-0.5">Gabinete</label>
                            <select name="id_gabinete" id="id_gabinete" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm w-48">
                                <option value="">Todos</option>
                                @foreach ($gabinetes as $g)
                                    <option value="{{ $g->id }}" {{ request('id_gabinete') == $g->id ? 'selected' : '' }}>{{ $g->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="q" class="block text-xs font-medium text-gray-500 mb-0.5">Pesquisar</label>
                            <input type="search" name="q" id="q" value="{{ request('q') }}"
                                   placeholder="Designação ou requerente..."
                                   class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary w-56 text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">
                            Filtrar
                        </button>
                        @if (request()->hasAny(['status', 'id_gabinete', 'q']))
                            <a href="{{ route('orcamentos.index') }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">
                                Limpar
                            </a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nº</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Designação</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requerente</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Imóvel</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gabinete</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($orcamentos as $orcamento)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $orcamento->numero ?? $orcamento->id }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $badges = [
                                                'rascunho' => 'bg-gray-100 text-gray-800',
                                                'enviado' => 'bg-blue-100 text-blue-800',
                                                'aceite' => 'bg-green-100 text-green-800',
                                                'recusado' => 'bg-red-100 text-red-800',
                                                'convertido' => 'bg-epoc-lighter text-epoc-primary',
                                                'faturado' => 'bg-emerald-100 text-emerald-800',
                                            ];
                                            $c = $badges[$orcamento->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded {{ $c }}">{{ $orcamento->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($orcamento->designacao, 40) ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $orcamento->requerente?->nome ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($orcamento->imovel?->morada, 25) ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $orcamento->gabinete?->nome ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('orcamentos.edit', $orcamento) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Editar</a>
                                        <form action="{{ route('orcamentos.destroy', $orcamento) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este orçamento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum orçamento encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($orcamentos->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $orcamentos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
