<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('orcamentos.index') }}" class="text-gray-500 hover:text-gray-700">Orçamentos</a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Novo orçamento
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('orcamentos.store') }}" class="p-6">
                    @csrf
                    @include('orcamentos._form', ['orcamento' => null])
                    @include('orcamentos._itens', ['orcamento' => null])
                    <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                        <x-primary-button>Guardar orçamento</x-primary-button>
                        <a href="{{ route('orcamentos.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
