@props(['orcamento' => null, 'readonly' => false])

@php
    $distritos = $distritos ?? collect();
    $tipo_imoveis = $tipo_imoveis ?? collect();
    $processos = $processos ?? collect();
    $imoveisDetalhes = ($imoveis ?? collect())->keyBy('id')->map(fn ($i) => [
        'morada' => $i->morada,
        'nip' => $i->nip,
        'codigo_postal' => $i->codigo_postal,
        'localidade' => $i->localidade,
        'coordenadas' => $i->coordenadas,
        'tipo_imovel' => $i->tipoImovel?->tipo_imovel,
        'distrito' => $i->distrito?->nome,
        'concelho' => $i->concelho?->nome,
        'freguesia' => $i->freguesia?->nome,
    ])->toArray();
    $modoInicial = old('id_processo', $orcamento?->id_processo) ? 'processo_existente' : (!empty(array_filter(old('novo_imovel', []))) ? 'novo' : ($orcamento ? 'existente' : 'novo'));
    $processosDetalhes = $processos->keyBy('id')->map(fn ($p) => $p->imovel ? [
        'referencia' => $p->referencia,
        'morada' => $p->imovel->morada,
        'nip' => $p->imovel->nip,
        'codigo_postal' => $p->imovel->codigo_postal,
        'localidade' => $p->imovel->localidade,
        'coordenadas' => $p->imovel->coordenadas,
        'tipo_imovel' => $p->imovel->tipoImovel?->tipo_imovel,
        'distrito' => $p->imovel->distrito?->nome,
        'concelho' => $p->imovel->concelho?->nome,
        'freguesia' => $p->imovel->freguesia?->nome,
    ] : null)->filter()->toArray();
@endphp
<div class="border border-gray-200 rounded-lg p-4 bg-gray-50/50" x-data="formImovel({{ json_encode($readonly) }}, {{ json_encode(old('id_imovel', $orcamento?->id_imovel)) }}, {{ json_encode(old('id_processo', $orcamento?->id_processo)) }}, @js($imoveisDetalhes), @js($processosDetalhes))">
    @if (!$readonly)
    <div class="flex flex-wrap items-center gap-4 mb-4">
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="radio" name="imovel_modo" value="processo_existente" x-model="modo" class="text-epoc-primary focus:ring-epoc-primary">
            <span class="text-sm font-medium text-gray-700">Processo existente</span>
        </label>
        @if ($orcamento)
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="radio" name="imovel_modo" value="existente" x-model="modo" class="text-epoc-primary focus:ring-epoc-primary">
            <span class="text-sm font-medium text-gray-700">Imóvel existente</span>
        </label>
        @endif
        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="radio" name="imovel_modo" value="novo" x-model="modo" class="text-epoc-primary focus:ring-epoc-primary">
            <span class="text-sm font-medium text-gray-700">{{ $orcamento ? 'Criar novo imóvel' : 'Novo imóvel' }}</span>
        </label>
    </div>
    @if (!$orcamento)
    <p class="text-xs text-gray-500 mb-2">Se o imóvel já existir, escolha o processo existente. Caso contrário, crie um novo imóvel.</p>
    @endif
    @endif

    {{-- Processo existente (dropdown) — carrega imóvel do processo --}}
    @if (!$readonly)
    <div x-show="modo === 'processo_existente'" x-cloak class="mb-4">
        <input type="hidden" name="id_processo" :value="modo === 'processo_existente' ? id_processo_selecionado : ''">
        <x-input-label for="id_processo" value="Processo" />
        <select id="id_processo" class="mt-1 block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm"
                x-model="id_processo_selecionado" :disabled="modo !== 'processo_existente'">
            <option value="">— Selecionar —</option>
            @foreach ($processos as $p)
                @php
                    $ref = $p->referencia ?? ('#' . $p->id);
                    $morada = $p->imovel?->morada ?: $p->imovel?->localidade ?: '—';
                @endphp
                <option value="{{ $p->id }}" {{ old('id_processo', $orcamento?->id_processo) == $p->id ? 'selected' : '' }}>{{ $ref }} — {{ Str::limit($morada, 60) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('id_processo')" class="mt-1" />
        <p class="mt-1 text-xs text-gray-500">O imóvel do processo será associado ao orçamento.</p>
    </div>
    <div x-show="modo === 'processo_existente' && id_processo_selecionado && processosDetalhes[id_processo_selecionado]" x-cloak class="mt-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Imóvel do processo selecionado</h4>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <div><dt class="text-gray-500">Referência</dt><dd class="font-medium text-gray-900" x-text="(processosDetalhes[id_processo_selecionado] || {}).referencia || '—'"></dd></div>
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium text-gray-900" x-text="(processosDetalhes[id_processo_selecionado] || {}).tipo_imovel || '—'"></dd></div>
            <div><dt class="text-gray-500">Morada</dt><dd class="font-medium text-gray-900" x-text="(processosDetalhes[id_processo_selecionado] || {}).morada || '—'"></dd></div>
            <div><dt class="text-gray-500">NIP</dt><dd class="font-medium text-gray-900" x-text="(processosDetalhes[id_processo_selecionado] || {}).nip || '—'"></dd></div>
            <div><dt class="text-gray-500">Localidade</dt><dd class="font-medium text-gray-900" x-text="(processosDetalhes[id_processo_selecionado] || {}).localidade || '—'"></dd></div>
            <div><dt class="text-gray-500">Distrito</dt><dd class="font-medium text-gray-900" x-text="(processosDetalhes[id_processo_selecionado] || {}).distrito || '—'"></dd></div>
        </dl>
    </div>
    @endif

    {{-- Imóvel existente (dropdown) — apenas na edição --}}
    @if (!$readonly && $orcamento)
    <div x-show="modo === 'existente'" x-cloak class="mb-4">
        <x-input-label for="id_imovel" value="Imóvel" />
        <select id="id_imovel" name="id_imovel" class="mt-1 block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm"
                x-model="id_imovel_selecionado" :disabled="modo !== 'existente'">
            <option value="">— Selecionar —</option>
            @foreach ($imoveis as $i)
                @php
                    $parte1 = $i->tipoImovel?->tipo_imovel ?? 'Imóvel';
                    $parte2 = $i->localidade ?: ($i->nip ?: $i->morada) ?: '—';
                    $parte3 = trim(($i->morada ?? '') . ($i->concelho?->nome ? ', ' . $i->concelho->nome : ''));
                    $parte4 = $i->distrito?->nome ?? '';
                    $descricao = trim(implode(' - ', array_filter([$parte1, $parte2, $parte3, $parte4], fn ($x) => $x !== '')));
                    if ($descricao === '') { $descricao = 'Imóvel #' . $i->id; }
                @endphp
                <option value="{{ $i->id }}" {{ old('id_imovel', $orcamento?->id_imovel) == $i->id ? 'selected' : '' }}>{{ Str::limit($descricao, 100) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('id_imovel')" class="mt-1" />
    </div>
    @endif

    {{-- Detalhes do imóvel (card com toda a informação) --}}
    @if ($readonly && !$orcamento?->imovel)
    <p class="text-sm text-gray-500">Nenhum imóvel associado.</p>
    @elseif ($readonly && $orcamento?->imovel)
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Dados do imóvel</h4>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->tipoImovel?->tipo_imovel ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Morada</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->morada ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">NIP</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->nip ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Código postal</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->codigo_postal ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Localidade</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->localidade ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Coordenadas</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->coordenadas ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Distrito</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->distrito?->nome ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Concelho</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->concelho?->nome ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Freguesia</dt><dd class="font-medium text-gray-900">{{ $orcamento->imovel->freguesia?->nome ?? '—' }}</dd></div>
        </dl>
    </div>
    @elseif (!$readonly && $orcamento)
    <div x-show="modo === 'existente' && id_imovel_selecionado && imoveisDetalhes[id_imovel_selecionado]" x-cloak class="mt-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Dados do imóvel selecionado</h4>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
            <div><dt class="text-gray-500">Tipo</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).tipo_imovel || '—'"></dd></div>
            <div><dt class="text-gray-500">Morada</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).morada || '—'"></dd></div>
            <div><dt class="text-gray-500">NIP</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).nip || '—'"></dd></div>
            <div><dt class="text-gray-500">Código postal</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).codigo_postal || '—'"></dd></div>
            <div><dt class="text-gray-500">Localidade</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).localidade || '—'"></dd></div>
            <div><dt class="text-gray-500">Coordenadas</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).coordenadas || '—'"></dd></div>
            <div><dt class="text-gray-500">Distrito</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).distrito || '—'"></dd></div>
            <div><dt class="text-gray-500">Concelho</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).concelho || '—'"></dd></div>
            <div><dt class="text-gray-500">Freguesia</dt><dd class="font-medium text-gray-900" x-text="(imoveisDetalhes[id_imovel_selecionado] || {}).freguesia || '—'"></dd></div>
        </dl>
    </div>
    @endif

    {{-- Novo imóvel (campos inline) --}}
    @if (!$readonly)
    <div x-show="modo === 'novo'" x-cloak class="space-y-4">
        <p class="text-sm text-gray-600">Preencha os dados do imóvel. Pode preencher apenas parte (ex.: morada) e completar depois no CRUD de imóveis.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-input-label for="novo_imovel_morada" value="Morada" />
                <x-text-input id="novo_imovel_morada" name="novo_imovel[morada]" type="text" class="mt-1 block w-full"
                              value="{{ old('novo_imovel.morada') }}" />
                <x-input-error :messages="$errors->get('novo_imovel.morada')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="novo_imovel_nip" value="NIP" />
                <x-text-input id="novo_imovel_nip" name="novo_imovel[nip]" type="text" class="mt-1 block w-full"
                              value="{{ old('novo_imovel.nip') }}" />
                <x-input-error :messages="$errors->get('novo_imovel.nip')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="novo_imovel_codigo_postal" value="Código postal" />
                <x-text-input id="novo_imovel_codigo_postal" name="novo_imovel[codigo_postal]" type="text" class="mt-1 block w-full"
                              value="{{ old('novo_imovel.codigo_postal') }}" />
                <x-input-error :messages="$errors->get('novo_imovel.codigo_postal')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="novo_imovel_localidade" value="Localidade" />
                <x-text-input id="novo_imovel_localidade" name="novo_imovel[localidade]" type="text" class="mt-1 block w-full"
                              value="{{ old('novo_imovel.localidade') }}" />
                <x-input-error :messages="$errors->get('novo_imovel.localidade')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="novo_imovel_coordenadas" value="Coordenadas" />
                <x-text-input id="novo_imovel_coordenadas" name="novo_imovel[coordenadas]" type="text" class="mt-1 block w-full"
                              value="{{ old('novo_imovel.coordenadas') }}" placeholder="ex: 38.7223, -9.1393" />
                <x-input-error :messages="$errors->get('novo_imovel.coordenadas')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="novo_imovel_id_tipo_imovel" value="Tipo de imóvel" />
                <select id="novo_imovel_id_tipo_imovel" name="novo_imovel[id_tipo_imovel]" class="mt-1 block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm">
                    <option value="">— Selecionar —</option>
                    @foreach ($tipo_imoveis as $t)
                        <option value="{{ $t->id }}" {{ old('novo_imovel.id_tipo_imovel') == $t->id ? 'selected' : '' }}>{{ $t->tipo_imovel }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('novo_imovel.id_tipo_imovel')" class="mt-1" />
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <x-input-label for="novo_imovel_id_distrito" value="Distrito" />
                <select id="novo_imovel_id_distrito" name="novo_imovel[id_distrito]" class="mt-1 block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm"
                        x-model="id_distrito" @change="carregarConcelhos()">
                    <option value="">— Selecionar —</option>
                    @foreach ($distritos as $d)
                        <option value="{{ $d->id_distrito }}" {{ old('novo_imovel.id_distrito') == $d->id_distrito ? 'selected' : '' }}>{{ $d->nome }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('novo_imovel.id_distrito')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="novo_imovel_id_concelho" value="Concelho" />
                <select id="novo_imovel_id_concelho" name="novo_imovel[id_concelho]" class="mt-1 block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm"
                        x-model="id_concelho" @change="carregarFreguesias()">
                    <option value="">— Selecionar —</option>
                    <template x-for="c in concelhos" :key="c.id_concelho">
                        <option :value="c.id_concelho" x-text="c.nome"></option>
                    </template>
                </select>
                <x-input-error :messages="$errors->get('novo_imovel.id_concelho')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="novo_imovel_id_freguesia" value="Freguesia" />
                <select id="novo_imovel_id_freguesia" name="novo_imovel[id_freguesia]" class="mt-1 block w-full border-gray-300 focus:border-epoc-primary focus:ring-epoc-primary rounded-md shadow-sm"
                        x-model="id_freguesia">
                    <option value="">— Selecionar —</option>
                    <template x-for="f in freguesias" :key="f.id_freguesia">
                        <option :value="f.id_freguesia" x-text="f.nome"></option>
                    </template>
                </select>
                <x-input-error :messages="$errors->get('novo_imovel.id_freguesia')" class="mt-1" />
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function formImovel(readonly, idImovelInicial, idProcessoInicial, imoveisDetalhes, processosDetalhes) {
    return {
        readonly: !!readonly,
        modo: '{{ $modoInicial }}',
        id_imovel_selecionado: idImovelInicial ? String(idImovelInicial) : '',
        id_processo_selecionado: idProcessoInicial ? String(idProcessoInicial) : '',
        imoveisDetalhes: imoveisDetalhes || {},
        processosDetalhes: processosDetalhes || {},
        id_distrito: '{{ old('novo_imovel.id_distrito') }}',
        id_concelho: '{{ old('novo_imovel.id_concelho') }}',
        id_freguesia: '{{ old('novo_imovel.id_freguesia') }}',
        concelhos: [],
        freguesias: [],
        carregarConcelhos() {
            this.id_concelho = '';
            this.id_freguesia = '';
            this.freguesias = [];
            if (!this.id_distrito) { this.concelhos = []; return Promise.resolve(); }
            return fetch(`/api/distritos/${this.id_distrito}/concelhos`)
                .then(r => r.json())
                .then(data => { this.concelhos = data; });
        },
        carregarFreguesias() {
            this.id_freguesia = '';
            if (!this.id_concelho) { this.freguesias = []; return Promise.resolve(); }
            return fetch(`/api/concelhos/${this.id_concelho}/freguesias`)
                .then(r => r.json())
                .then(data => { this.freguesias = data; });
        },
        init() {
            if (this.id_distrito) {
                this.carregarConcelhos().then(() => {
                    if (this.id_concelho) this.carregarFreguesias();
                });
            }
        }
    };
}
</script>
