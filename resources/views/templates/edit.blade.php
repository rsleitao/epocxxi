<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('templates.index') }}" class="text-gray-500 hover:text-gray-700">Templates</a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar {{ $template->nome }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('templates.update', $template) }}" enctype="multipart/form-data" class="p-6 space-y-4" data-unsaved-warn>
                    @csrf
                    @method('PUT')
                    <div>
                        <p class="text-sm text-gray-500">Tipo de documento: <strong>{{ $template->documentoTipo->nome }}</strong> (não editável)</p>
                    </div>
                    <div>
                        <x-input-label for="nome" value="Nome do template *" />
                        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" :value="old('nome', $template->nome)" required />
                        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="ficheiro" value="Substituir ficheiro (opcional)" />
                        <input type="file" id="ficheiro" name="ficheiro" accept=".docx"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-epoc-lighter file:text-epoc-primary hover:file:bg-epoc-light">
                        <p class="mt-1 text-xs text-gray-500">Ficheiro atual: {{ $template->nome_original ?? $template->ficheiro }}</p>
                        <x-input-error :messages="$errors->get('ficheiro')" class="mt-1" />
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_predefinido" value="1" {{ old('is_predefinido', $template->is_predefinido) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-epoc-primary shadow-sm focus:ring-epoc-primary">
                            <span class="ml-2 text-sm text-gray-600">Usar como predefinido para este tipo</span>
                        </label>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <x-primary-button>Atualizar</x-primary-button>
                        <a href="{{ route('templates.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
