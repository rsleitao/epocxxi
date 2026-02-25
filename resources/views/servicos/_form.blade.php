@props(['servico' => null])

<div class="space-y-4">
    <div>
        <x-input-label for="codigo" value="Código" />
        <x-text-input id="codigo" name="codigo" type="text" class="mt-1 block w-full"
                      value="{{ old('codigo', $servico?->codigo) }}" />
        <x-input-error :messages="$errors->get('codigo')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="nome" value="Nome *" />
        <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
                      value="{{ old('nome', $servico?->nome) }}" required />
        <x-input-error :messages="$errors->get('nome')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="descricao" value="Descrição" />
        <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao', $servico?->descricao) }}</textarea>
        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="unidade" value="Unidade" />
            <x-text-input id="unidade" name="unidade" type="text" class="mt-1 block w-full"
                          value="{{ old('unidade', $servico?->unidade) }}" placeholder="ex: unidade, hora, m²" />
            <x-input-error :messages="$errors->get('unidade')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="preco_base" value="Preço base (€)" />
            <x-text-input id="preco_base" name="preco_base" type="number" step="0.01" min="0" class="mt-1 block w-full"
                          value="{{ old('preco_base', $servico?->preco_base ?? '0') }}" />
            <x-input-error :messages="$errors->get('preco_base')" class="mt-1" />
        </div>
    </div>
    <div>
        <x-input-label for="tipo_trabalho" value="Tipo de trabalho" />
        <x-text-input id="tipo_trabalho" name="tipo_trabalho" type="text" class="mt-1 block w-full"
                      value="{{ old('tipo_trabalho', $servico?->tipo_trabalho) }}" placeholder="ex: instalação, vistoria" />
        <x-input-error :messages="$errors->get('tipo_trabalho')" class="mt-1" />
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="ativo" value="0">
        <input id="ativo" name="ativo" type="checkbox" value="1" {{ old('ativo', $servico?->ativo ?? true) ? 'checked' : '' }}
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        <x-input-label for="ativo" value="Ativo" class="!mb-0" />
        <x-input-error :messages="$errors->get('ativo')" class="mt-1" />
    </div>
</div>
