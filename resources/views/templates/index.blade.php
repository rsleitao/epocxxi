<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Templates
            </h2>
            <a href="{{ route('templates.create') }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo template
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">Filtre por tipo ou pesquise por nome. Para ver os placeholders de cada tipo: <a href="{{ route('documento-tipos.index') }}" class="text-epoc-primary hover:text-epoc-primary-hover">Tipos de documento</a> → clique no tipo.</p>
                    <form method="get" action="{{ route('templates.index') }}" class="flex gap-2 flex-wrap items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tipo de documento</label>
                            <select name="id_documento_tipo" class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                                <option value="">— Todos —</option>
                                @foreach ($tipos as $t)
                                    <option value="{{ $t->id }}" {{ request('id_documento_tipo') == $t->id ? 'selected' : '' }}>{{ $t->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Nome..."
                                   class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm w-48">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">Filtrar</button>
                        @if (request()->hasAny(['id_documento_tipo', 'q']))
                            <a href="{{ route('templates.index') }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">Limpar</a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ficheiro</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Predefinido</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($templates as $tpl)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $tpl->id }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $tpl->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $tpl->documentoTipo->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $tpl->nome_original ?? $tpl->ficheiro }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $tpl->is_predefinido ? 'Sim' : '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('documento-tipos.show', $tpl->documentoTipo) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Ver tipo</a>
                                        <a href="{{ route('templates.edit', $tpl) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Editar</a>
                                        <form action="{{ route('templates.destroy', $tpl) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este template?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Nenhum template. Crie primeiro um tipo de documento em Tipos de documento e depois adicione um ficheiro Word (.docx) com placeholders ${campo}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    {{ $templates->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
