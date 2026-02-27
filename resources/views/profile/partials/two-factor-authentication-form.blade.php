<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Autenticação de dois fatores (2FA)') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Aumente a segurança da sua conta com um código do Google Authenticator, Authy ou outra app compatível.') }}
        </p>
    </header>

    @if (session('status') === 'two-factor-authentication-enabled')
        <div class="mb-4 font-medium text-sm text-amber-600">
            {{ __('Termine de configurar a autenticação de dois fatores abaixo.') }}
        </div>
    @endif

    @if ($user->two_factor_secret)
        @if (! $user->two_factor_confirmed_at)
            <div class="mt-4 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ __('Escaneie este código QR com a sua aplicação de autenticação ou introduza a chave manualmente.') }}
                </p>
                <div class="inline-block p-2 bg-white border rounded">
                    {!! $user->twoFactorQrCodeSvg() !!}
                </div>

                <form method="post" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="mt-4">
                    @csrf
                    <div>
                        <x-input-label for="code" :value="__('Código de verificação')" />
                        <x-text-input id="code" name="code" type="text" inputmode="numeric" class="mt-1 block w-full" autocomplete="one-time-code" />
                        <x-input-error class="mt-2" :messages="$errors->get('code')" />
                    </div>
                    <x-primary-button class="mt-4">{{ __('Confirmar') }}</x-primary-button>
                </form>
            </div>
        @else
            <div class="mt-4 space-y-4">
                @if (session('status') === 'two-factor-authentication-confirmed')
                    <p class="font-medium text-sm text-green-600">
                        {{ __('Autenticação de dois fatores ativada com sucesso.') }}
                    </p>
                @endif

                <p class="text-sm text-gray-600">
                    {{ __('Guarde estes códigos de recuperação num local seguro. Cada código só pode ser usado uma vez.') }}
                </p>
                <div class="grid gap-2 grid-cols-2 font-mono text-sm p-4 bg-gray-50 rounded">
                    @foreach ((array) $user->recoveryCodes() as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>

                <div class="flex gap-4 mt-4">
                    <form method="post" action="{{ url('/user/two-factor-recovery-codes') }}">
                        @csrf
                        <x-secondary-button type="submit">{{ __('Regenerar códigos de recuperação') }}</x-secondary-button>
                    </form>
                    <form method="post" action="{{ url('/user/two-factor-authentication') }}" class="inline"
                        onsubmit="return confirm('{{ __('Tem a certeza que deseja desativar a autenticação de dois fatores?') }}');">
                        @csrf
                        @method('delete')
                        <x-danger-button type="submit">{{ __('Desativar 2FA') }}</x-danger-button>
                    </form>
                </div>
            </div>
        @endif
    @else
        <form method="post" action="{{ url('/user/two-factor-authentication') }}" class="mt-4">
            @csrf
            <x-primary-button type="submit">{{ __('Ativar autenticação de dois fatores') }}</x-primary-button>
        </form>
        <p class="mt-2 text-xs text-gray-500">
            {{ __('Será pedida a confirmação da sua palavra-passe antes de ativar.') }}
        </p>
    @endif
</section>
