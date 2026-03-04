<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar utilizador
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="post" action="{{ route('users.update', $user) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password (opcional)</label>
                                <input type="password" name="password"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Confirmar password</label>
                                <input type="password" name="password_confirmation"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Função</label>

                            @if ($user->isProtectedAdmin())
                                <div class="mt-1 text-sm text-gray-900">Administrador (protegido)</div>
                                <input type="hidden" name="role" value="admin">
                                <p class="mt-1 text-xs text-gray-500">
                                    Esta conta é o administrador principal e não pode perder a função de Administrador.
                                </p>
                            @else
                                <select name="role"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                                    <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Funcionário</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                            @endif

                            @error('role')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @php
                            $selectedPermissions = old('permissions', array_keys($user->permissions ?? []));

                            $permissionGroups = [
                                'Requerentes' => [
                                    'requerentes.view' => 'Ver lista / detalhes',
                                    'requerentes.create' => 'Criar',
                                    'requerentes.edit' => 'Editar',
                                    'requerentes.delete' => 'Apagar',
                                ],
                                'Gabinetes' => [
                                    'gabinetes.view' => 'Ver lista / detalhes',
                                    'gabinetes.create' => 'Criar',
                                    'gabinetes.edit' => 'Editar',
                                    'gabinetes.delete' => 'Apagar',
                                ],
                                'Subcontratados' => [
                                    'subcontratados.view' => 'Ver lista / detalhes',
                                    'subcontratados.create' => 'Criar',
                                    'subcontratados.edit' => 'Editar',
                                    'subcontratados.delete' => 'Apagar',
                                ],
                                'Tipos de imóvel' => [
                                    'tipo-imoveis.view' => 'Ver lista / detalhes',
                                    'tipo-imoveis.create' => 'Criar',
                                    'tipo-imoveis.edit' => 'Editar',
                                    'tipo-imoveis.delete' => 'Apagar',
                                ],
                                'Serviços' => [
                                    'servicos.view' => 'Ver lista / detalhes',
                                    'servicos.create' => 'Criar',
                                    'servicos.edit' => 'Editar',
                                    'servicos.delete' => 'Apagar',
                                ],
                                'Orçamentos' => [
                                    'orcamentos.view' => 'Ver lista / detalhes',
                                    'orcamentos.create' => 'Criar',
                                    'orcamentos.edit' => 'Editar',
                                    'orcamentos.delete' => 'Apagar',
                                ],
                                'Processos' => [
                                    'processos.view' => 'Ver lista / detalhes',
                                ],
                                'Trabalhos' => [
                                    'trabalhos.view' => 'Ver lista / detalhes',
                                    'trabalhos.edit' => 'Editar / marcar concluído',
                                ],
                            ];
                        @endphp

                        <div class="pt-4 border-t border-gray-200 mt-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-2">Permissões</h3>
                            <p class="text-xs text-gray-500 mb-4">
                                Define o que este utilizador pode fazer em cada área. Administradores têm sempre acesso total; estas permissões são mais relevantes para Funcionários.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($permissionGroups as $groupLabel => $perms)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">{{ $groupLabel }}</h4>
                                        <div class="space-y-1">
                                            @foreach ($perms as $key => $label)
                                                <label class="flex items-center gap-2 text-xs text-gray-700">
                                                    <input
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $key }}"
                                                        class="rounded border-gray-300 text-epoc-primary shadow-sm focus:ring-epoc-primary"
                                                        {{ in_array($key, $selectedPermissions, true) ? 'checked' : '' }}
                                                        @if ($user->isProtectedAdmin()) disabled @endif
                                                    >
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-2">
                            <a href="{{ route('users.index') }}"
                               class="px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-epoc-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-epoc-primary-hover">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

