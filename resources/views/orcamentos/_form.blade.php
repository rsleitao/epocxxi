@props(['orcamento' => null, 'readonly' => false])

@php
    $designacaoInicial = old('designacao', $orcamento?->designacao);
    if ($designacaoInicial === '' && $orcamento) {
        $req = $orcamento->requerente;
        $gab = $orcamento->gabinete;
        $designacaoInicial = trim(($req?->nome ?? '') . ' ' . ($gab ? '(' . $gab->nome . ')' : ''));
    }
    $statusLabels = [
        'rascunho' => 'Rascunho',
        'enviado' => 'Enviado',
        'aceite' => 'Aceite',
        'recusado' => 'Recusado',
        'convertido' => 'Convertido',
        'faturado' => 'Faturado',
    ];
@endphp

@if ($readonly)
{{-- Modo faturado: dados como texto, mais apelativo --}}
<div class="space-y-6">
    <div class="rounded-lg border border-gray-200 bg-gray-50/50 p-5">
        <h3 class="text-sm font-medium text-gray-700 mb-4">Dados do orçamento</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
            <div>
                <dt class="text-gray-500 font-medium">Estado</dt>
                <dd class="mt-0.5 text-gray-900">{{ $statusLabels[$orcamento->status] ?? $orcamento->status }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500 font-medium">Designação</dt>
                <dd class="mt-0.5 text-gray-900">{{ $designacaoInicial ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Requerente</dt>
                <dd class="mt-0.5 text-gray-900">{{ $orcamento->requerente?->nome ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Faturar a</dt>
                <dd class="mt-0.5 text-gray-900">{{ $orcamento->requerenteFatura?->nome ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Gabinete</dt>
                <dd class="mt-0.5 text-gray-900">{{ $orcamento->gabinete?->nome ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Subcontratado</dt>
                <dd class="mt-0.5 text-gray-900">{{ $orcamento->subcontratado?->nome ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <div class="mt-4">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Imóvel</h3>
        @include('orcamentos._form_imovel', ['orcamento' => $orcamento ?? null, 'readonly' => true])
    </div>
</div>
@else
<div class="space-y-6" x-data="{
    designacao: @js($designacaoInicial),
    requerentes: @js($requerentes->pluck('nome', 'id')->toArray()),
    gabinetes: @js($gabinetes->pluck('nome', 'id')->toArray()),
    actualizarDesignacao() {
        const r = document.getElementById('id_requerente')?.value;
        const g = document.getElementById('id_gabinete')?.value;
        const rn = this.requerentes[r] || '';
        const gn = this.gabinetes[g] || '';
        this.designacao = rn + (gn ? ' (' + gn + ')' : '');
    }
}" x-init="actualizarDesignacao()">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="status" value="Estado *" />
            <select id="status" name="status" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="rascunho" {{ old('status', $orcamento?->status) === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                <option value="enviado" {{ old('status', $orcamento?->status) === 'enviado' ? 'selected' : '' }}>Enviado</option>
                <option value="aceite" {{ old('status', $orcamento?->status) === 'aceite' ? 'selected' : '' }}>Aceite</option>
                <option value="recusado" {{ old('status', $orcamento?->status) === 'recusado' ? 'selected' : '' }}>Recusado</option>
                <option value="convertido" {{ old('status', $orcamento?->status) === 'convertido' ? 'selected' : '' }}>Convertido</option>
                <option value="faturado" {{ old('status', $orcamento?->status) === 'faturado' ? 'selected' : '' }}>Faturado</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="designacao" value="Designação (preenchida pelo requerente e gabinete)" />
            <input type="text" id="designacao" name="designacao" x-model="designacao"
                   class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                   placeholder="Preenchido ao escolher requerente e gabinete">
            <x-input-error :messages="$errors->get('designacao')" class="mt-1" />
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <x-input-label for="id_requerente" value="Requerente" />
            <select id="id_requerente" name="id_requerente" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    @change="actualizarDesignacao()">
                <option value="">— Selecionar —</option>
                @foreach ($requerentes as $r)
                    <option value="{{ $r->id }}" {{ old('id_requerente', $orcamento?->id_requerente) == $r->id ? 'selected' : '' }}>{{ $r->nome }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('id_requerente')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="id_requerente_fatura" value="Faturar a" />
            <select id="id_requerente_fatura" name="id_requerente_fatura" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">— Selecionar —</option>
                @foreach ($requerentes as $r)
                    <option value="{{ $r->id }}" {{ old('id_requerente_fatura', $orcamento?->id_requerente_fatura) == $r->id ? 'selected' : '' }}>{{ $r->nome }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('id_requerente_fatura')" class="mt-1" />
        </div>
    </div>

    <div class="mt-4">
        <x-input-label value="Imóvel" class="mb-2 block" />
        @include('orcamentos._form_imovel', ['orcamento' => $orcamento ?? null, 'readonly' => false])
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        <div>
            <x-input-label for="id_gabinete" value="Gabinete" />
            <select id="id_gabinete" name="id_gabinete" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    @change="actualizarDesignacao()">
                <option value="">— Selecionar —</option>
                @foreach ($gabinetes as $g)
                    <option value="{{ $g->id }}" {{ old('id_gabinete', $orcamento?->id_gabinete) == $g->id ? 'selected' : '' }}>{{ $g->nome }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('id_gabinete')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="id_subcontratado" value="Subcontratado" />
            <select id="id_subcontratado" name="id_subcontratado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">— Nenhum —</option>
                @foreach ($subcontratados as $s)
                    <option value="{{ $s->id }}" {{ old('id_subcontratado', $orcamento?->id_subcontratado) == $s->id ? 'selected' : '' }}>{{ $s->nome }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('id_subcontratado')" class="mt-1" />
        </div>
    </div>
</div>
@endif
