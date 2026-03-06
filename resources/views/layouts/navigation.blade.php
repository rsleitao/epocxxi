<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('logo.png') }}" alt="EPOC" class="h-12 w-auto" style="max-height: 3rem; height: auto; width: auto;" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:gap-6 sm:ms-10">
                    @php
                        $user = auth()->user();
                        $trabalhoAtual = null;
                        $trabalhoAtualPayload = null;
                        if ($user && $user->hasPermission('trabalhos.view')) {
                            $trabalhoAtual = \App\Models\OrcamentoItem::query()
                                ->whereHas('orcamento', fn ($q) => $q->where('status', 'em_execucao'))
                                ->whereIn('estado', ['em_execucao', 'pendente'])
                                ->where('id_user', $user->id)
                                ->with(['servico', 'orcamento.processo', 'tempoSegmentos'])
                                ->orderBy('id')
                                ->first();
                            if ($trabalhoAtual) {
                                $trabalhoAtual->load(['tempoSegmentos']);
                                $trabalhoAtualPayload = [
                                    'id' => $trabalhoAtual->id,
                                    'estado' => $trabalhoAtual->estado,
                                    'servico_nome' => $trabalhoAtual->servico?->nome ?? 'Serviço ocasional',
                                    'processo_ref' => $trabalhoAtual->orcamento->processo?->referencia,
                                    'orcamento_numero' => $trabalhoAtual->orcamento->numero ?? '#'.$trabalhoAtual->orcamento->id,
                                    'tempo_total_formatado' => $trabalhoAtual->tempo_total_formatado,
                                    'tempo_a_correr' => $trabalhoAtual->hasTempoAberto(),
                                    'tempo_started_at' => $trabalhoAtual->tempo_started_at,
                                    'update_estado_url' => route('trabalhos.update-estado', $trabalhoAtual),
                                    'target_estado' => $trabalhoAtual->estado === 'em_execucao' ? 'pendente' : 'em_execucao',
                                ];
                            }
                        }
                    @endphp

                    {{-- Orçamentos (link direto) --}}
                    @if ($user && $user->hasPermission('orcamentos.view'))
                        <a href="{{ route('orcamentos.index') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('orcamentos.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Orçamentos
                        </a>
                    @endif

                    {{-- Processos (link direto) --}}
                    @if ($user && $user->hasPermission('processos.view'))
                        <a href="{{ route('processos.index') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('processos.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Processos
                        </a>
                    @endif

                    {{-- Trabalhos (link direto) --}}
                    @if ($user && $user->hasPermission('trabalhos.view'))
                        <a href="{{ route('trabalhos.index') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('trabalhos.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Trabalhos
                        </a>
                    @endif

                    {{-- Gestão (link direto) --}}
                    <a href="{{ route('gestao.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-base font-semibold leading-5 transition duration-150 ease-in-out {{ request()->routeIs('gestao.*', 'requerentes.*', 'gabinetes.*', 'subcontratados.*', 'tipo-imoveis.*', 'servicos.*', 'documento-tipos.*', 'templates.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Gestão
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                @if ($user && $user->hasPermission('trabalhos.edit'))
                    <div class="hidden md:flex items-center"
                         x-data="headerTrabalhoAtual(@js($trabalhoAtualPayload))"
                         x-show="trabalho"
                         x-cloak
                         @trabalho-atual-atualizado.window="trabalho = $event.detail">
                        <div class="px-3 py-1.5 rounded-full border border-epoc-primary/40 bg-epoc-primary/5 text-xs text-gray-700 flex items-center gap-2">
                            <div class="flex flex-col">
                                <span class="font-semibold text-epoc-primary" x-text="trabalho ? (trabalho.estado === 'em_execucao' ? 'Trabalho em execução' : 'Trabalho pausado') : ''"></span>
                                <span class="text-gray-600">
                                    <span x-text="trabalho ? trabalho.servico_nome : ''"></span>
                                    <span x-show="trabalho && trabalho.processo_ref" x-text="' · Proc. ' + trabalho.processo_ref"></span>
                                    <span x-text="trabalho ? ' · Orç. ' + trabalho.orcamento_numero : ''"></span>
                                </span>
                            </div>
                            <span class="flex items-center gap-1 text-gray-700">
                                <span x-text="trabalho ? trabalho.tempo_total_formatado : ''"></span>
                                <span x-show="trabalho && trabalho.tempo_a_correr" class="inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            </span>
                            <button type="button"
                                    x-show="trabalho"
                                    @click="toggleEstado()"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-xs font-semibold transition"
                                    :class="trabalho && trabalho.target_estado === 'pendente' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                                <template x-if="trabalho && trabalho.target_estado === 'pendente'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6 4a1 1 0 00-1 1v10a1 1 0 102 0V5a1 1 0 00-1-1zm6 0a1 1 0 00-1 1v10a1 1 0 102 0V5a1 1 0 00-1-1z" />
                                    </svg>
                                </template>
                                <template x-if="trabalho && trabalho.target_estado === 'em_execucao'">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.5 4.5a1 1 0 011.5-.866l7 4.5a1 1 0 010 1.732l-7 4.5A1 1 0 016 13.5v-9z" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                    </div>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @php $user = auth()->user(); @endphp

            @if ($user && $user->hasPermission('orcamentos.view'))
                <div class="px-4 pt-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Orçamentos</div>
                <x-responsive-nav-link :href="route('orcamentos.index')" :active="request()->routeIs('orcamentos.*')">
                    Orçamentos
                </x-responsive-nav-link>
            @endif

            @if ($user && $user->hasPermission('processos.view'))
                <div class="px-4 pt-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Processos</div>
                <x-responsive-nav-link :href="route('processos.index')" :active="request()->routeIs('processos.*')">
                    Processos
                </x-responsive-nav-link>
            @endif

            @if ($user && $user->hasPermission('trabalhos.view'))
                <div class="px-4 pt-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Trabalhos</div>
                <x-responsive-nav-link :href="route('trabalhos.index')" :active="request()->routeIs('trabalhos.*')">
                    Trabalhos
                </x-responsive-nav-link>
            @endif

            <div class="px-4 pt-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestão</div>
            <x-responsive-nav-link :href="route('gestao.index')" :active="request()->routeIs('gestao.*', 'requerentes.*', 'gabinetes.*', 'subcontratados.*', 'tipo-imoveis.*', 'servicos.*', 'documento-tipos.*', 'templates.*')">
                Gestão
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('headerTrabalhoAtual', function (initial) {
            return {
                trabalho: initial,
                toggleEstado: function () {
                    var self = this;
                    if (!self.trabalho || !self.trabalho.update_estado_url) return;
                    var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content') || '';
                    fetch(self.trabalho.update_estado_url, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ estado: self.trabalho.target_estado })
                    }).then(function (r) { return r.json(); }).then(function (data) {
                        if (data.ok && data.hasOwnProperty('trabalho_atual')) {
                            self.trabalho = data.trabalho_atual;
                            window.dispatchEvent(new CustomEvent('trabalho-estado-alterado-header', { detail: data.trabalho_atual }));
                        }
                    }).catch(function () {
                        alert('Erro de ligação ao servidor.');
                    });
                }
            };
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        var csrf = document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').content || '';
        document.querySelectorAll('[data-trabalho-estado-url]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var url = this.dataset.trabalhoEstadoUrl;
                var target = this.dataset.trabalhoEstadoTarget;
                if (!url || !target) return;

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ estado: target })
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (data.ok) {
                        if (data.hasOwnProperty('trabalho_atual')) {
                            window.dispatchEvent(new CustomEvent('trabalho-atual-atualizado', { detail: data.trabalho_atual }));
                        }
                        window.location.reload();
                    } else {
                        var modalErro = document.getElementById('modal-erro-trabalho');
                        var modalErroText = document.getElementById('modal-erro-trabalho-text');
                        if (modalErro && modalErroText) {
                            modalErroText.textContent = data.message || 'Erro ao atualizar estado do trabalho.';
                            modalErro.classList.remove('hidden');
                            modalErro.classList.add('flex');
                        } else {
                            alert(data.message || 'Erro ao atualizar estado do trabalho.');
                        }
                    }
                }).catch(function () {
                    var modalErro = document.getElementById('modal-erro-trabalho');
                    var modalErroText = document.getElementById('modal-erro-trabalho-text');
                    if (modalErro && modalErroText) {
                        modalErroText.textContent = 'Erro de ligação ao servidor.';
                        modalErro.classList.remove('hidden');
                        modalErro.classList.add('flex');
                    } else {
                        alert('Erro de ligação ao servidor.');
                    }
                });
            });
        });
    });
</script>
