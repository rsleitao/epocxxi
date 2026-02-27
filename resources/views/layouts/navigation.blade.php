<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('logo.png') }}" alt="EPOC" class="h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:gap-1 sm:ms-10">
                    {{-- Orçamentos (link direto) --}}
                    <a href="{{ route('orcamentos.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('orcamentos.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Orçamentos
                    </a>

                    {{-- Processos (link direto) --}}
                    <a href="{{ route('processos.index') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('processos.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Processos
                    </a>

                    {{-- Trabalhos --}}
                    <x-dropdown align="left" width="w-52" contentClasses="py-1">
                        <x-slot name="trigger">
                            <span class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 cursor-pointer transition duration-150 ease-in-out {{ request()->routeIs('trabalhos.*', 'servicos.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Trabalhos
                                <svg class="ms-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </span>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('trabalhos.index')">Trabalhos</x-dropdown-link>
                            <x-dropdown-link :href="route('servicos.index')">Serviços</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    {{-- Gestão --}}
                    <x-dropdown align="left" width="w-56" contentClasses="py-1">
                        <x-slot name="trigger">
                            <span class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 cursor-pointer transition duration-150 ease-in-out {{ request()->routeIs('requerentes.*', 'gabinetes.*', 'subcontratados.*', 'tipo-imoveis.*', 'documento-tipos.*', 'templates.*') ? 'border-epoc-primary text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Gestão
                                <svg class="ms-0.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </span>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('requerentes.index')">Requerentes</x-dropdown-link>
                            <x-dropdown-link :href="route('gabinetes.index')">Gabinetes</x-dropdown-link>
                            <x-dropdown-link :href="route('subcontratados.index')">Subcontratados</x-dropdown-link>
                            <x-dropdown-link :href="route('tipo-imoveis.index')">Tipos de imóvel</x-dropdown-link>
                            <div class="border-t border-gray-100 my-1"></div>
                            <x-dropdown-link :href="route('documento-tipos.index')">Tipos de documento</x-dropdown-link>
                            <x-dropdown-link :href="route('templates.index')">Templates</x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
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
            <div class="px-4 pt-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Orçamentos</div>
            <x-responsive-nav-link :href="route('orcamentos.index')" :active="request()->routeIs('orcamentos.*')">
                Orçamentos
            </x-responsive-nav-link>
            <div class="px-4 pt-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Trabalhos</div>
            <x-responsive-nav-link :href="route('trabalhos.index')" :active="request()->routeIs('trabalhos.*')">
                Trabalhos
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('servicos.index')" :active="request()->routeIs('servicos.*')">
                Serviços
            </x-responsive-nav-link>
            <div class="px-4 pt-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestão</div>
            <x-responsive-nav-link :href="route('requerentes.index')" :active="request()->routeIs('requerentes.*')">
                Requerentes
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('gabinetes.index')" :active="request()->routeIs('gabinetes.*')">
                Gabinetes
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('subcontratados.index')" :active="request()->routeIs('subcontratados.*')">
                Subcontratados
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tipo-imoveis.index')" :active="request()->routeIs('tipo-imoveis.*')">
                Tipos de imóvel
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('documento-tipos.index')" :active="request()->routeIs('documento-tipos.*')">
                Tipos de documento
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('templates.index')" :active="request()->routeIs('templates.*')">
                Templates
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
