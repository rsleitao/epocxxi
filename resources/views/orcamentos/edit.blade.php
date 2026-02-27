@php
    $readonly = in_array($orcamento->status, ['aceite', 'em_execucao', 'por_faturar', 'faturado']);
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('orcamentos.index') }}" class="text-gray-500 hover:text-gray-700">Orçamentos</a>
                <span class="text-gray-400">/</span>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $readonly ? 'Ver orçamento' : 'Editar orçamento' }} {{ $orcamento->numero ? 'nº ' . $orcamento->numero : '#' . $orcamento->id }}
                </h2>
                @if ($readonly)
                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-amber-100 text-amber-800">Apenas consulta e impressão</span>
                @endif
            </div>
            <div class="flex items-center gap-3 flex-wrap" x-data="{ imprimirOpen: false }">
                <div class="relative">
                    <button type="button" @click="imprimirOpen = !imprimirOpen"
                            class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover focus:bg-epoc-primary-hover active:bg-epoc-primary-hover focus:outline-none focus:ring-2 focus:ring-epoc-primary focus:ring-offset-2 transition ease-in-out duration-150">
                        Imprimir
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="imprimirOpen" x-cloak @click.away="imprimirOpen = false"
                         class="absolute right-0 top-full mt-1 z-10 py-1 bg-white border border-gray-200 rounded-md shadow-lg min-w-[220px]">
                        <a href="{{ route('orcamentos.report', $orcamento) }}" target="_blank"
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Ver relatório (HTML)
                        </a>
                        @if (isset($templatesOrcamento) && $templatesOrcamento->isNotEmpty())
                            <div class="border-t border-gray-100 mt-1 pt-1">
                                @foreach ($templatesOrcamento as $tpl)
                                    <a href="{{ route('orcamentos.gerar-documento', [$orcamento, $tpl]) }}"
                                       class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ $tpl->nome }} (Word){{ $tpl->is_predefinido ? ' — predefinido' : '' }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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
                            <a href="{{ route('orcamentos.index') }}" class="inline-flex items-center px-4 py-2 bg-epoc-primary text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-epoc-primary-hover">
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
