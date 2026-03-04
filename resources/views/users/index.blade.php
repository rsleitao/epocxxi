<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Utilizadores
            </h2>
            <a href="{{ route('users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                Novo utilizador
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-data="{ modalOpen: false, deleteUserUrl: '', deleteUserName: '' }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Função</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if ($user->role === 'admin')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-epoc-primary/10 text-epoc-primary">
                                                Administrador
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                Funcionário
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right space-x-2">
                                        @if (auth()->id() === $user->id)
                                            <a href="{{ route('profile.edit') }}" class="text-epoc-primary hover:text-epoc-primary-hover">Perfil</a>
                                        @else
                                            <a href="{{ route('users.edit', $user) }}" class="text-epoc-primary hover:text-epoc-primary-hover">Editar</a>

                                            @if (! $user->isProtectedAdmin())
                                                <button type="button"
                                                        class="text-red-600 hover:text-red-900"
                                                        @click="modalOpen = true; deleteUserUrl = @js(route('users.destroy', $user)); deleteUserName = @js($user->name)">
                                                    Eliminar
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">
                                        Nenhum utilizador encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>

                <!-- Modal apagar utilizador -->
                <div
                    x-show="modalOpen"
                    x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                    @keydown.escape.window="modalOpen = false"
                >
                    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4" @click.outside="modalOpen = false">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-900">Eliminar utilizador</h3>
                        </div>
                        <div class="px-4 py-4 text-sm text-gray-700">
                            <p>Tens a certeza que queres eliminar este utilizador?</p>
                            <p class="mt-2 font-semibold text-gray-900" x-text="deleteUserName"></p>
                            <p class="mt-2 text-xs text-red-600">
                                Esta ação é definitiva e não pode ser anulada.
                            </p>
                        </div>
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                            <button type="button"
                                    class="px-3 py-1.5 rounded-md border border-gray-300 text-xs font-medium text-gray-700 hover:bg-gray-100"
                                    @click="modalOpen = false">
                                Cancelar
                            </button>
                            <form :action="deleteUserUrl" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-md bg-red-600 text-xs font-semibold text-white hover:bg-red-700">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

