<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('documento-tipos.index') }}" class="text-gray-500 hover:text-gray-700">Tipos de documento</a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo tipo de documento</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form data-unsaved-warn method="post" action="{{ route('documento-tipos.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="nome" value="Nome *" />
                        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" :value="old('nome')" required />
                        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="slug" value="Slug *" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono" :value="old('slug')" placeholder="ex: orcamento" required />
                        <p class="mt-1 text-xs text-gray-500">Apenas letras minúsculas, números, hífen e underscore. Define onde o tipo é usado (ex: <code>orcamento</code> para orçamentos).</p>
                        <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="descricao" value="Descrição" />
                        <textarea id="descricao" name="descricao" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-epoc-primary focus:ring-epoc-primary">{{ old('descricao') }}</textarea>
                        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
                    </div>
                    <div class="flex gap-3 pt-4">
                        <x-primary-button>Guardar</x-primary-button>
                        <a href="{{ route('documento-tipos.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
