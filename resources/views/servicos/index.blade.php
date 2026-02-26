<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Serviços
            </h2>
            <a href="{{ route('servicos.create') }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo serviço
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <form method="get" action="{{ route('servicos.index') }}" class="flex gap-2 flex-wrap">
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Código ou nome..."
                               class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary w-64">
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">
                            Pesquisar
                        </button>
                        @if (request('q'))
                            <a href="{{ route('servicos.index') }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">
                                Limpar
                            </a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unidade</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Preço base</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Ativo</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($servicos as $servico)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $servico->id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $servico->codigo ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $servico->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $servico->unidade ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">{{ number_format($servico->preco_base, 2, ',', ' ') }} €</td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        @if ($servico->ativo)
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800">Sim</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-600">Não</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('servicos.edit', $servico) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Editar</a>
                                        <form action="{{ route('servicos.destroy', $servico) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este serviço?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum serviço encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($servicos->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $servicos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
