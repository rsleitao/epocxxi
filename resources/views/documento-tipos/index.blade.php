@php
    $crudTiposEnabled = config('app.document_type_crud_enabled', true);
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tipos de documento
            </h2>
            @if ($crudTiposEnabled)
                <a href="{{ route('documento-tipos.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                    Novo tipo
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <p class="mb-4 text-sm text-red-600">{{ session('error') }}</p>
            @endif
            @if (!$crudTiposEnabled)
                <p class="mb-4 text-sm text-gray-600">Lista de tipos de documento e placeholders disponíveis para cada um. Para adicionar templates, use o link «Ver campos e templates».</p>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <form method="get" action="{{ route('documento-tipos.index') }}" class="flex gap-2 flex-wrap">
                        <input type="search" name="q" value="{{ request('q') }}"
                               placeholder="Nome ou slug..."
                               class="rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary w-64">
                        <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium hover:bg-gray-300">Pesquisar</button>
                        @if (request('q'))
                            <a href="{{ route('documento-tipos.index') }}" class="px-4 py-2 text-gray-600 text-sm hover:underline">Limpar</a>
                        @endif
                    </form>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Templates</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ $crudTiposEnabled ? 'Ações' : 'Ação' }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($tipos as $tipo)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $tipo->id }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $tipo->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600"><code>{{ $tipo->slug }}</code></td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $tipo->templates_count }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        <a href="{{ route('documento-tipos.show', $tipo) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Ver campos e templates</a>
                                        @if ($crudTiposEnabled)
                                            <a href="{{ route('documento-tipos.edit', $tipo) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Editar</a>
                                            <form action="{{ route('documento-tipos.destroy', $tipo) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este tipo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        @if ($crudTiposEnabled)
                                            Nenhum tipo de documento. Crie um tipo (ex: Orçamento com slug <code>orcamento</code>) para depois adicionar templates.
                                        @else
                                            Nenhum tipo de documento registado.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    {{ $tipos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
