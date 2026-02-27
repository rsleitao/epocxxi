<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Introduza o código do seu aplicativo autenticador ou um código de recuperação.') }}
    </div>

    <form method="POST" action="{{ url('/two-factor-challenge') }}" class="space-y-4">
        @csrf

        <div x-data="{ recovery: false }">
            <div x-show="! recovery">
                <x-input-label for="code" :value="__('Código')" />
                <x-text-input id="code"
                    class="block mt-1 w-full"
                    type="text"
                    inputmode="numeric"
                    name="code"
                    autofocus
                    autocomplete="one-time-code"
                />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div x-show="recovery" x-cloak>
                <x-input-label for="recovery_code" :value="__('Código de recuperação')" />
                <x-text-input id="recovery_code"
                    class="block mt-1 w-full"
                    type="text"
                    name="recovery_code"
                    autocomplete="one-time-code"
                />
                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="button"
                    class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                    x-show="! recovery"
                    x-on:click="recovery = true; $nextTick(() => $refs.recovery_code.focus())"
                >
                    {{ __('Usar código de recuperação') }}
                </button>
                <button type="button"
                    class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                    x-show="recovery"
                    x-cloak
                    x-on:click="recovery = false; $nextTick(() => $refs.code.focus())"
                >
                    {{ __('Usar código da app') }}
                </button>
            </div>
        </div>

        <div class="flex justify-end">
            <x-primary-button>
                {{ __('Verificar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
