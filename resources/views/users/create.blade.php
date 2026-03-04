<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Novo utilizador
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="post" action="{{ route('users.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password</label>
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
                            <select name="role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-epoc-primary focus:ring-epoc-primary text-sm">
                                <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>Funcionário</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
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

