@php
    $calendario = $calendario ?? [
        'calendarioMes' => now()->format('Y-m'),
        'daysInMonth' => now()->daysInMonth(),
        'firstWeekday' => now()->copy()->startOfMonth()->isoWeekday() - 1,
        'datasComTrabalhos' => [],
        'mesAnoLabel' => now()->locale('pt')->translatedFormat('F Y'),
        'mesPrev' => now()->copy()->subMonth()->format('Y-m'),
        'mesNext' => now()->copy()->addMonth()->format('Y-m'),
        'hoje' => now()->format('Y-m-d'),
    ];
    $trabalhosPorDataJson = $trabalhosPorDataJson ?? '{}';
    $trabalhos = $trabalhos ?? collect();
    $calendarioJson = json_encode($calendario);
    $trabalhosJson = $trabalhosPorDataJson;
@endphp
<x-app-layout>
    {{-- Dados do calendário no head para existirem antes do Alpine --}}
    @push('scripts')
    <script>
        window.__dashboardCalendario = {!! $calendarioJson !!};
        window.__dashboardTrabalhos = {!! $trabalhosJson !!};
    </script>
    @endpush
    <div class="py-8" x-data="dashboardCalendar(window.__dashboardCalendario || {}, window.__dashboardTrabalhos || {})">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Coluna esquerda: trabalhos por ordem de prazo --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Trabalhos por fazer</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Por ordem de prazo (em execução)</p>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[calc(100vh-12rem)] overflow-y-auto">
                            @forelse ($trabalhos as $item)
                                <div class="p-4 hover:bg-gray-50/50 flex items-center justify-between gap-4">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900">{{ $item->servico?->nome ?? 'Serviço ocasional' }}</p>
                                        <p class="text-sm text-gray-500 truncate">
                                            Orç. {{ $item->orcamento->numero ?? '#' . $item->orcamento->id }}
                                            · {{ $item->orcamento->requerente?->nome ?? '—' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        @if ($item->prazo_data)
                                            <span class="text-sm font-medium {{ $item->prazo_data->isPast() ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ $item->prazo_data->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">Sem prazo</span>
                                        @endif
                                        <a href="{{ route('trabalhos.index') }}" class="text-sm text-epoc-primary hover:underline">Ir para trabalhos</a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-500">
                                    Nenhum trabalho pendente. Os trabalhos aparecem aqui quando um orçamento está «Em execução».
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Coluna direita: calendário (Alpine, semana a começar na segunda) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-4">
                        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                            <button type="button" @click="loadMonth(mesPrev)" class="text-gray-500 hover:text-gray-800 p-1 rounded">←</button>
                            <span class="font-semibold text-gray-800 capitalize" x-text="mesAnoLabel"></span>
                            <button type="button" @click="loadMonth(mesNext)" class="text-gray-500 hover:text-gray-800 p-1 rounded">→</button>
                        </div>
                        <div class="p-3">
                            <div class="grid grid-cols-7 gap-0.5 text-center text-xs font-medium text-gray-500 mb-1">
                                <span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span><span>Dom</span>
                            </div>
                            <div class="grid grid-cols-7 gap-0.5" x-ref="calGrid">
                                <template x-for="slot in emptySlots" :key="'empty-'+slot">
                                    <div class="aspect-square rounded bg-gray-100/50"></div>
                                </template>
                                <template x-for="d in dayNumbers" :key="d">
                                    <button type="button"
                                            :disabled="!getDataStr(d) || !datasComTrabalhos.includes(getDataStr(d))"
                                            @click="getDataStr(d) && datasComTrabalhos.includes(getDataStr(d)) && openModal(getDataStr(d))"
                                            class="aspect-square rounded text-sm font-medium flex items-center justify-center transition relative overflow-visible border border-transparent"
                                            :style="getCellStyle(d)">
                                        <span class="inline-block w-full text-center text-base font-semibold" x-text="d"></span>
                                        <span x-show="isHoje(d)" class="absolute top-0.5 right-0.5 z-10 w-2.5 h-2.5 rounded-full animate-pulse" style="background-color: #22c55e;" aria-hidden="true"></span>
                                    </button>
                                </template>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-500">
                                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full" style="background-color: #fee2e2; border: 1px solid #b91c1c;"></span> Trabalhos em atraso</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full" style="background-color: #2D647A;"></span> Com trabalhos</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Hoje</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal: trabalhos do dia --}}
        <div x-show="modalOpen"
             x-cloak
             x-transition
             @keydown.escape.window="modalOpen = false"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full max-h-[80vh] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900" x-text="modalData ? 'Trabalhos para ' + modalData : ''"></h3>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 p-1">✕</button>
                </div>
                <div class="p-4 overflow-y-auto flex-1">
                    <template x-if="modalItens && modalItens.length">
                        <ul class="space-y-3">
                            <template x-for="item in modalItens" :key="item.id">
                                <li class="flex justify-between gap-2 text-sm border-b border-gray-100 pb-2 last:border-0">
                                    <span class="font-medium text-gray-900" x-text="item.servico"></span>
                                    <span class="text-gray-500 shrink-0" x-text="'Orç. ' + item.orcamento_numero"></span>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <p x-show="modalItens && !modalItens.length" class="text-gray-500 text-sm">Nenhum trabalho nesta data.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardCalendar', (calendario, trabalhosPorData) => {
                const c = calendario && typeof calendario === 'object' ? calendario : {};
                return {
                calendarioMes: c.calendarioMes || '',
                daysInMonth: Number(c.daysInMonth) || 31,
                firstWeekday: Number(c.firstWeekday) || 0,
                datasComTrabalhos: Array.isArray(c.datasComTrabalhos) ? c.datasComTrabalhos : [],
                trabalhosPorData: trabalhosPorData && typeof trabalhosPorData === 'object' ? trabalhosPorData : {},
                mesAnoLabel: c.mesAnoLabel || '',
                mesPrev: c.mesPrev || '',
                mesNext: c.mesNext || '',
                hoje: c.hoje || '',
                modalOpen: false,
                modalData: '',
                modalItens: [],
                get emptySlots() {
                    return Array.from({ length: this.firstWeekday }, (_, i) => i);
                },
                get dayNumbers() {
                    return Array.from({ length: this.daysInMonth }, (_, i) => i + 1);
                },
                getDataStr(d) {
                    if (!d || !this.calendarioMes) return '';
                    const pad = n => String(n).padStart(2, '0');
                    return this.calendarioMes + '-' + pad(d);
                },
                isFimDeSemana(dataStr) {
                    if (!dataStr) return false;
                    const d = new Date(dataStr + 'T12:00:00');
                    const day = d.getDay();
                    return day === 0 || day === 6;
                },
                isHoje(d) {
                    const dataStr = this.getDataStr(d);
                    const h = String(this.hoje || '').split('T')[0];
                    return !!dataStr && dataStr === h;
                },
                getCellStyle(d) {
                    const dataStr = this.getDataStr(d);
                    if (!dataStr) return { backgroundColor: '#f9fafb', color: '#111827', cursor: 'default' };
                    const comTrabalhos = Array.isArray(this.datasComTrabalhos) && this.datasComTrabalhos.includes(dataStr);
                    const fimSemana = this.isFimDeSemana(dataStr);
                    const hojeStr = String(this.hoje || '').split('T')[0];
                    const emAtraso = comTrabalhos && hojeStr && dataStr < hojeStr;
                    if (emAtraso) {
                        // Dia com trabalhos cujo prazo já passou: realce em vermelho suave
                        return { backgroundColor: '#fee2e2', color: '#b91c1c', cursor: 'pointer', fontWeight: 600 };
                    }
                    if (comTrabalhos) return { backgroundColor: '#AECAD9', color: '#2D647A', cursor: 'pointer' };
                    if (fimSemana) return { backgroundColor: '#e2e8f0', color: '#1e293b', cursor: 'default' };
                    return { backgroundColor: '#f9fafb', color: '#1f2937', cursor: 'default' };
                },
                openModal(dataStr) {
                    this.modalItens = this.trabalhosPorData[dataStr] || [];
                    const [y, m, d] = dataStr.split('-');
                    this.modalData = d + '/' + m + '/' + y;
                    this.modalOpen = true;
                },
                async loadMonth(mes) {
                    const url = '{{ route("dashboard") }}?mes=' + encodeURIComponent(mes);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.calendarioMes = data.calendarioMes;
                    this.daysInMonth = data.daysInMonth;
                    this.firstWeekday = data.firstWeekday;
                    this.datasComTrabalhos = data.datasComTrabalhos;
                    this.trabalhosPorData = data.trabalhosPorData;
                    this.mesAnoLabel = data.mesAnoLabel;
                    this.mesPrev = data.mesPrev;
                    this.mesNext = data.mesNext;
                    if (data.hoje) this.hoje = data.hoje;
                }
            };
            });
        });
    </script>
</x-app-layout>
