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

                @php
                    $collection = $templates->getCollection();
                    $partesWord = $collection->filter(function ($tpl) {
                        return optional($tpl->documentoTipo)->slug === 'parteescritas'
                            && strtolower(pathinfo($tpl->ficheiro, PATHINFO_EXTENSION)) === 'docx';
                    });
                    $partesExcel = $collection->filter(function ($tpl) {
                        return optional($tpl->documentoTipo)->slug === 'parteescritas'
                            && strtolower(pathinfo($tpl->ficheiro, PATHINFO_EXTENSION)) === 'xlsx';
                    });
                    $orcWord = $collection->filter(function ($tpl) {
                        return optional($tpl->documentoTipo)->slug === 'orcamento'
                            && strtolower(pathinfo($tpl->ficheiro, PATHINFO_EXTENSION)) === 'docx';
                    });
                    $orcExcel = $collection->filter(function ($tpl) {
                        return optional($tpl->documentoTipo)->slug === 'orcamento'
                            && strtolower(pathinfo($tpl->ficheiro, PATHINFO_EXTENSION)) === 'xlsx';
                    });
                    $outros = $collection->filter(function ($tpl) {
                        $slug = optional($tpl->documentoTipo)->slug;
                        return $slug !== 'parteescritas' && $slug !== 'orcamento';
                    });
                @endphp

                <div class="p-4 space-y-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Coluna: Partes Escritas --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-800">Partes Escritas</h3>
                                <p class="text-xs text-gray-500">Templates usados para gerar documentos de Partes Escritas (processos).</p>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Word (.docx)</span>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-full">Nome</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Predefinido</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-32 min-w-[6rem] whitespace-nowrap">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @forelse ($partesWord as $tpl)
                                                    <tr>
                                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 w-full">{{ $tpl->nome }}</td>
                                                        <td class="px-3 py-2 text-sm text-center w-24">{{ $tpl->is_predefinido ? 'Sim' : '—' }}</td>
                                                        <td class="px-3 py-2 text-sm text-right space-x-1 w-32 min-w-[6rem] whitespace-nowrap">
                                                            <a href="{{ route('documento-tipos.show', $tpl->documentoTipo) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Ver tipo">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('templates.edit', $tpl) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Editar">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2 2 0 112.828 2.828L11.828 13.828a4 4 0 01-1.414.94L7 16l1.232-3.414a4 4 0 01.94-1.414z" />
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('templates.destroy', $tpl) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este template?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="inline-flex items-center justify-center h-7 w-7 rounded-full text-red-600 hover:text-red-700 hover:bg-red-50"
                                                                        title="Eliminar">
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-sm">Nenhum template Word de Partes Escritas neste filtro.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="p-3 bg-white/60">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Excel (.xlsx)</span>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-full">Nome</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Predefinido</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-32 min-w-[6rem] whitespace-nowrap">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @forelse ($partesExcel as $tpl)
                                                    <tr>
                                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 w-full">{{ $tpl->nome }}</td>
                                                        <td class="px-3 py-2 text-sm text-center w-24">{{ $tpl->is_predefinido ? 'Sim' : '—' }}</td>
                                                        <td class="px-3 py-2 text-sm text-right space-x-1 w-32 min-w-[6rem] whitespace-nowrap">
                                                            <a href="{{ route('documento-tipos.show', $tpl->documentoTipo) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Ver tipo">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('templates.edit', $tpl) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Editar">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2 2 0 112.828 2.828L11.828 13.828a4 4 0 01-1.414.94L7 16l1.232-3.414a4 4 0 01.94-1.414z" />
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('templates.destroy', $tpl) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este template?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="inline-flex items-center justify-center h-7 w-7 rounded-full text-red-600 hover:text-red-700 hover:bg-red-50"
                                                                        title="Eliminar">
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-sm">Nenhum template Excel de Partes Escritas neste filtro.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Coluna: Orçamentos --}}
                        <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-800">Orçamentos</h3>
                                <p class="text-xs text-gray-500">Templates usados para gerar documentos de Orçamentos.</p>
                            </div>
                            <div class="divide-y divide-gray-200">
                                <div class="p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Word (.docx)</span>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-full">Nome</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Predefinido</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-32 min-w-[6rem] whitespace-nowrap">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @forelse ($orcWord as $tpl)
                                                    <tr>
                                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 w-full">{{ $tpl->nome }}</td>
                                                        <td class="px-3 py-2 text-sm text-center w-24">{{ $tpl->is_predefinido ? 'Sim' : '—' }}</td>
                                                        <td class="px-3 py-2 text-sm text-right space-x-1 w-32 min-w-[6rem] whitespace-nowrap">
                                                            <a href="{{ route('documento-tipos.show', $tpl->documentoTipo) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Ver tipo">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('templates.edit', $tpl) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Editar">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2 2 0 112.828 2.828L11.828 13.828a4 4 0 01-1.414.94L7 16l1.232-3.414a4 4 0 01.94-1.414z" />
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('templates.destroy', $tpl) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este template?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="inline-flex items-center justify-center h-7 w-7 rounded-full text-red-600 hover:text-red-700 hover:bg-red-50"
                                                                        title="Eliminar">
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-sm">Nenhum template Word de Orçamentos neste filtro.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="p-3 bg-white/60">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Excel (.xlsx)</span>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-full">Nome</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Predefinido</th>
                                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase w-32 min-w-[6rem] whitespace-nowrap">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @forelse ($orcExcel as $tpl)
                                                    <tr>
                                                        <td class="px-3 py-2 text-sm font-medium text-gray-900 w-full">{{ $tpl->nome }}</td>
                                                        <td class="px-3 py-2 text-sm text-center w-24">{{ $tpl->is_predefinido ? 'Sim' : '—' }}</td>
                                                        <td class="px-3 py-2 text-sm text-right space-x-1 w-32 min-w-[6rem] whitespace-nowrap">
                                                            <a href="{{ route('documento-tipos.show', $tpl->documentoTipo) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Ver tipo">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('templates.edit', $tpl) }}"
                                                               class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                               title="Editar">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2 2 0 112.828 2.828L11.828 13.828a4 4 0 01-1.414.94L7 16l1.232-3.414a4 4 0 01.94-1.414z" />
                                                                </svg>
                                                            </a>
                                                            <form action="{{ route('templates.destroy', $tpl) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este template?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                        class="inline-flex items-center justify-center h-7 w-7 rounded-full text-red-600 hover:text-red-700 hover:bg-red-50"
                                                                        title="Eliminar">
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="px-3 py-4 text-center text-gray-500 text-sm">Nenhum template Excel de Orçamentos neste filtro.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($outros->isNotEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-800">Outros tipos de documento</h3>
                                <p class="text-xs text-gray-500">Templates de outros tipos que não são Orçamentos nem Partes Escritas.</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-full">Nome</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase w-24">Predefinido</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase w-32 min-w-[6rem] whitespace-nowrap">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($outros as $tpl)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900 w-full">{{ $tpl->nome }}</td>
                                                <td class="px-4 py-3 text-sm text-center w-24">{{ $tpl->is_predefinido ? 'Sim' : '—' }}</td>
                                                <td class="px-4 py-3 text-sm text-right space-x-1 w-32 min-w-[6rem] whitespace-nowrap">
                                                    <a href="{{ route('documento-tipos.show', $tpl->documentoTipo) }}"
                                                       class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                       title="Ver tipo">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('templates.edit', $tpl) }}"
                                                       class="inline-flex items-center justify-center h-7 w-7 rounded-full text-epoc-primary hover:text-epoc-primary-hover hover:bg-epoc-primary/10"
                                                       title="Editar">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2 2 0 112.828 2.828L11.828 13.828a4 4 0 01-1.414.94L7 16l1.232-3.414a4 4 0 01.94-1.414z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('templates.destroy', $tpl) }}" method="post" class="inline" onsubmit="return confirm('Eliminar este template?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center justify-center h-7 w-7 rounded-full text-red-600 hover:text-red-700 hover:bg-red-50"
                                                                title="Eliminar">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-4 border-t border-gray-200">
                    {{ $templates->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
