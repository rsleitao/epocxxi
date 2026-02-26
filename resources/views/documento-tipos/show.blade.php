<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('documento-tipos.index') }}" class="text-gray-500 hover:text-gray-700">Tipos de documento</a>
                <span class="text-gray-400">/</span>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $documentoTipo->nome }}</h2>
            </div>
            <a href="{{ route('templates.create', ['id_documento_tipo' => $documentoTipo->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo template
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Campos disponíveis (placeholders) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700">Campos disponíveis para este tipo</h3>
                    <p class="text-xs text-gray-500 mt-1">Use estes placeholders no seu documento Word. No ficheiro, escreva ex: <code>${designacao}</code> e o sistema substitui pelo valor ao gerar o documento. Ao gerar a partir de um orçamento (Editar orçamento → «Gerar documento (Word)»), pode escolher qual template usar nesta lista.</p>
                </div>
                <div class="p-4">
                    @if (count($campos) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Placeholder</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                        <th class="px-3 py-2 w-24"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($campos as $chave => $descricao)
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-gray-900">{{ \App\Services\DocumentoCamposService::placeholder($chave) }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $descricao }}</td>
                                            <td class="px-3 py-2">
                                                <button type="button" onclick="navigator.clipboard.writeText('{{ \App\Services\DocumentoCamposService::placeholder($chave) }}'); this.textContent='Copiado!'; setTimeout(() => this.textContent='Copiar', 1500)"
                                                        class="text-xs text-epoc-primary hover:text-epoc-primary-hover">Copiar</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Não há campos definidos para o slug <code>{{ $documentoTipo->slug }}</code>. Adicione-os em <code>DocumentoCamposService</code>.</p>
                    @endif
                </div>
            </div>

            {{-- Templates associados --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700">Templates</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ficheiro</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Predefinido</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($documentoTipo->templates as $tpl)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $tpl->nome }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $tpl->nome_original ?? $tpl->ficheiro }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $tpl->is_predefinido ? 'Sim' : '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
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
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">Nenhum template. Crie um documento Word com os placeholders acima, guarde como .docx e faça upload em «Novo template».</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
