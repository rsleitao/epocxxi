<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestão
                </h2>
                <p class="mt-1 text-sm text-gray-600 max-w-2xl">
                    Painel central com todos os atalhos de configuração e gestão do sistema. Escolha abaixo o que pretende gerir.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Requerentes</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Gere a lista de requerentes para associar a orçamentos e processos.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('requerentes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                            Abrir requerentes
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Gabinetes</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Defina e organize os gabinetes responsáveis pelos processos.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('gabinetes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                            Abrir gabinetes
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Subcontratados</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Mantenha a lista de técnicos e entidades subcontratadas atualizada.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('subcontratados.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                            Abrir subcontratados
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tipos de imóvel</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Configure os tipos de imóvel usados na caracterização dos processos.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('tipo-imoveis.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                            Abrir tipos de imóvel
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Serviços</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Defina os serviços prestados e respetivos valores base para orçamentos.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('servicos.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                            Abrir serviços
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tipos de documento</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Configure os tipos de documento e respetivos campos disponíveis.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('documento-tipos.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                            Abrir tipos de documento
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Templates</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Faça a gestão dos templates Word e Excel usados para gerar documentos.
                        </p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('templates.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                            Abrir templates
                        </a>
                    </div>
                </div>

                @if (auth()->user()?->isAdmin())
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Relatórios</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Trabalhos concluídos, tempo por gabinete e técnico. Filtros por período para visão da produtividade.
                            </p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('gestao.relatorios') }}"
                               class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                                Abrir relatórios
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Utilizadores</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Crie contas para novos colaboradores e defina se são administradores ou funcionários.
                            </p>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('users.index') }}"
                               class="inline-flex items-center px-3 py-2 bg-epoc-primary text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-epoc-primary-hover">
                                Gerir utilizadores
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

