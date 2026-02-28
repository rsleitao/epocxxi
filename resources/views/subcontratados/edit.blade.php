<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('subcontratados.index') }}" class="text-gray-500 hover:text-gray-700">Subcontratados</a>
            <span class="text-gray-400">/</span>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar subcontratado
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="post" action="{{ route('subcontratados.update', $subcontratado) }}" class="p-6 space-y-6" data-unsaved-warn>
                    @csrf
                    @method('PUT')
                    @include('subcontratados._form', ['subcontratado' => $subcontratado])
                    <div class="flex gap-3">
                        <x-primary-button>Atualizar</x-primary-button>
                        <a href="{{ route('subcontratados.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
