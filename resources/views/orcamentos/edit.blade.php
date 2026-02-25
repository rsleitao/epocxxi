@php
    $readonly = $orcamento->status === 'faturado';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('orcamentos.index') }}" class="text-gray-500 hover:text-gray-700">Orçamentos</a>
                <span class="text-gray-400">/</span>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $readonly ? 'Ver orçamento' : 'Editar orçamento' }} #{{ $orcamento->id }}
                </h2>
                @if ($readonly)
                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-emerald-100 text-emerald-800">Faturado — apenas consulta</span>
                @endif
            </div>
            @if ($orcamento->historico->isNotEmpty())
                <a href="#historico" class="text-sm text-indigo-600 hover:text-indigo-800">
                    Ver histórico
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($readonly)
                    <div class="p-6">
                        @include('orcamentos._form', ['orcamento' => $orcamento, 'readonly' => true])
                        @include('orcamentos._itens', ['orcamento' => $orcamento, 'readonly' => true])
                        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                            <a href="{{ route('orcamentos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700">
                                Voltar à lista
                            </a>
                        </div>
                    </div>
                @else
                    <form method="post" action="{{ route('orcamentos.update', $orcamento) }}" class="p-6">
                        @csrf
                        @method('PUT')
                        @include('orcamentos._form', ['orcamento' => $orcamento, 'readonly' => false])
                        @include('orcamentos._itens', ['orcamento' => $orcamento, 'readonly' => false])
                        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                            <x-primary-button>Atualizar orçamento</x-primary-button>
                            <a href="{{ route('orcamentos.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                                Cancelar
                            </a>
                        </div>
                    </form>
                @endif
            </div>

            @if ($orcamento->historico->isNotEmpty())
                <div id="historico" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg scroll-mt-4">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700">Histórico de estados</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Criação e alterações de estado do orçamento.</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        @foreach ($orcamento->historico as $h)
                            <li class="px-4 py-3 flex flex-wrap items-baseline gap-x-2 gap-y-1 text-sm">
                                @if ($h->status_anterior === null)
                                    <span class="font-medium text-gray-900">Criado</span>
                                    <span class="text-gray-500">em {{ $h->created_at->format('d/m/Y H:i') }}</span>
                                    <span class="text-gray-500">como <span class="font-medium text-gray-700">{{ $h->status_novo }}</span></span>
                                @else
                                    <span class="text-gray-500">Passou de <span class="font-medium text-gray-700">{{ $h->status_anterior }}</span> para <span class="font-medium text-gray-700">{{ $h->status_novo }}</span></span>
                                    <span class="text-gray-500">em {{ $h->created_at->format('d/m/Y H:i') }}</span>
                                @endif
                                @if ($h->user)
                                    <span class="text-gray-400">por {{ $h->user->name }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
