@props(['gabinete' => null])

<div class="space-y-4">
    <div>
        <x-input-label for="nome" value="Nome *" />
        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                      value="{{ old('nome', $gabinete?->nome) }}" required />
        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="nif" value="NIF" />
        <x-text-input id="nif" name="nif" type="text" class="mt-1 block w-full"
                      value="{{ old('nif', $gabinete?->nif) }}" />
        <x-input-error :messages="$errors->get('nif')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="morada" value="Morada" />
        <x-text-input id="morada" name="morada" type="text" class="mt-1 block w-full"
                      value="{{ old('morada', $gabinete?->morada) }}" />
        <x-input-error :messages="$errors->get('morada')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="codigo_postal" value="Código postal" />
        <x-text-input id="codigo_postal" name="codigo_postal" type="text" class="mt-1 block w-full"
                      value="{{ old('codigo_postal', $gabinete?->codigo_postal) }}" />
        <x-input-error :messages="$errors->get('codigo_postal')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                      value="{{ old('email', $gabinete?->email) }}" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="telefone" value="Telefone" />
        <x-text-input id="telefone" name="telefone" type="text" class="mt-1 block w-full"
                      value="{{ old('telefone', $gabinete?->telefone) }}" />
        <x-input-error :messages="$errors->get('telefone')" class="mt-1" />
    </div>
</div>
