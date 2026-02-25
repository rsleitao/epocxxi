@props(['tipoImovel' => null])

<div class="space-y-4">
    <div>
        <x-input-label for="tipo_imovel" value="Tipo de imóvel *" />
        <x-text-input id="tipo_imovel" name="tipo_imovel" type="text" class="mt-1 block w-full"
                      value="{{ old('tipo_imovel', $tipoImovel?->tipo_imovel) }}" required />
        <x-input-error :messages="$errors->get('tipo_imovel')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="descricao" value="Descrição" />
        <textarea id="descricao" name="descricao" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao', $tipoImovel?->descricao) }}</textarea>
        <x-input-error :messages="$errors->get('descricao')" class="mt-1" />
    </div>
</div>
