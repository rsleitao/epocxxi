<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 text-gray-600">
                {{ __("You're logged in!") }}
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('requerentes.index') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <h3 class="font-semibold text-gray-900">Requerentes</h3>
                    <p class="mt-1 text-sm text-gray-500">Gerir requerentes (clientes).</p>
                </a>
                <a href="{{ route('gabinetes.index') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <h3 class="font-semibold text-gray-900">Gabinetes</h3>
                    <p class="mt-1 text-sm text-gray-500">Gerir gabinetes.</p>
                </a>
                <a href="{{ route('subcontratados.index') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <h3 class="font-semibold text-gray-900">Subcontratados</h3>
                    <p class="mt-1 text-sm text-gray-500">Gerir subcontratados.</p>
                </a>
                <a href="{{ route('tipo-imoveis.index') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <h3 class="font-semibold text-gray-900">Tipos de imóvel</h3>
                    <p class="mt-1 text-sm text-gray-500">Gerir tipos de imóvel.</p>
                </a>
                <a href="{{ route('servicos.index') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <h3 class="font-semibold text-gray-900">Serviços</h3>
                    <p class="mt-1 text-sm text-gray-500">Gerir serviços e preços base.</p>
                </a>
                <a href="{{ route('orcamentos.index') }}" class="block p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-50 transition">
                    <h3 class="font-semibold text-gray-900">Orçamentos</h3>
                    <p class="mt-1 text-sm text-gray-500">Criar e gerir orçamentos.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
