<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gabinetes
            </h2>
            <a href="{{ route('gabinetes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo gabinete
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <form method="get" action="{{ route('gabinetes.index') }}" class="flex gap-2 flex-wrap">
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Nome, NIF ou email..."
                               class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary w-64">
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">
                            Pesquisar
                        </button>
                        @if (request('q'))
                            <a href="{{ route('gabinetes.index') }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">
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
                            @forelse ($gabinetes as $gabinete)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $gabinete->id }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $gabinete->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $gabinete->nif ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $gabinete->email ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $gabinete->telefone ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('gabinetes.edit', $gabinete) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Editar</a>
                                        <form action="{{ route('gabinetes.destroy', $gabinete) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este gabinete?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum gabinete encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($gabinetes->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $gabinetes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
