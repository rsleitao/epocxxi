<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informação do perfil') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Atualize os seus dados pessoais e profissionais.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" data-unsaved-warn>
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-y-6 lg:grid-cols-2 lg:gap-x-8">
            {{-- Coluna esquerda: dados pessoais (um único bloco = separador contínuo) --}}
            <div class="space-y-6 lg:pr-8 lg:border-r lg:border-gray-200 lg:border-solid">
                <p class="text-sm font-medium text-gray-700">{{ __('Dados pessoais') }}</p>
                <div>
                    <x-input-label for="name" :value="__('Nome')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="mt-2 text-sm text-gray-800">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="underline text-gray-600 hover:text-gray-900">{{ __('Reenviar email de verificação') }}</button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm font-medium text-green-600">{{ __('Foi enviado um novo link de verificação.') }}</p>
                        @endif
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="cc" :value="__('CC')" />
                        <x-text-input id="cc" name="cc" type="text" class="mt-1 block w-full" :value="old('cc', $user->cc)" autocomplete="off" />
                        <x-input-error class="mt-2" :messages="$errors->get('cc')" />
                    </div>
                    <div>
                        <x-input-label for="nif" :value="__('NIF')" />
                        <x-text-input id="nif" name="nif" type="text" class="mt-1 block w-full" :value="old('nif', $user->nif)" autocomplete="off" />
                        <x-input-error class="mt-2" :messages="$errors->get('nif')" />
                    </div>
                </div>
            </div>

            {{-- Coluna direita: informação profissional --}}
            <div class="space-y-6 lg:pl-8">
                <p class="text-sm font-medium text-gray-700">{{ __('Informação profissional') }}</p>
                <div>
                    <x-input-label for="dgeg" :value="__('DGEG')" />
                    <x-text-input id="dgeg" name="dgeg" type="text" class="mt-1 block w-full" :value="old('dgeg', $user->dgeg)" autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('dgeg')" />
                </div>
                <div>
                    <x-input-label for="oet" :value="__('OET')" />
                    <x-text-input id="oet" name="oet" type="text" class="mt-1 block w-full" :value="old('oet', $user->oet)" autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('oet')" />
                </div>
                <div>
                    <x-input-label for="oe" :value="__('OE')" />
                    <x-text-input id="oe" name="oe" type="text" class="mt-1 block w-full" :value="old('oe', $user->oe)" autocomplete="off" />
                    <x-input-error class="mt-2" :messages="$errors->get('oe')" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>
