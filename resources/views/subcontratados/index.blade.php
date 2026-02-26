<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Subcontratados
            </h2>
            <a href="{{ route('subcontratados.create') }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo subcontratado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <form method="get" action="{{ route('subcontratados.index') }}" class="flex gap-2 flex-wrap">
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Nome, NIF ou email..."
                               class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary w-64">
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">
                            Pesquisar
                        </button>
                        @if (request('q'))
                            <a href="{{ route('subcontratados.index') }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">
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
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">NIF</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($subcontratados as $subcontratado)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $subcontratado->id }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $subcontratado->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $subcontratado->nif ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $subcontratado->email ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $subcontratado->telefone ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('subcontratados.edit', $subcontratado) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Editar</a>
                                        <form action="{{ route('subcontratados.destroy', $subcontratado) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este subcontratado?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum subcontratado encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($subcontratados->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $subcontratados->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
