<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use App\Models\Gabinete;
use App\Models\Imovel;
use App\Models\Orcamento;
use App\Models\OrcamentoHistorico;
use App\Models\OrcamentoItem;
use App\Models\Requerente;
use App\Models\Servico;
use App\Models\Subcontratado;
use App\Models\TipoImovel;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrcamentoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Orcamento::query()
            ->with(['requerente', 'imovel', 'gabinete'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('id_gabinete')) {
            $query->where('id_gabinete', $request->input('id_gabinete'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('designacao', 'like', "%{$q}%")
                    ->orWhereHas('requerente', fn ($r) => $r->where('nome', 'like', "%{$q}%"));
            });
        }

        $orcamentos = $query->paginate(15)->withQueryString();
        $gabinetes = Gabinete::orderBy('nome')->get();

        return view('orcamentos.index', compact('orcamentos', 'gabinetes'));
    }

    public function create(): View
    {
        return view('orcamentos.create', [
            'requerentes' => Requerente::orderBy('nome')->get(),
            'gabinetes' => Gabinete::orderBy('nome')->get(),
            'imoveis' => Imovel::with(['tipoImovel', 'distrito', 'concelho', 'freguesia'])->orderBy('morada')->get(),
            'subcontratados' => Subcontratado::orderBy('nome')->get(),
            'distritos' => Distrito::orderBy('nome')->get(),
            'tipo_imoveis' => TipoImovel::orderBy('tipo_imovel')->get(),
            'servicos' => Servico::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:rascunho,enviado,aceite,recusado,convertido,faturado',
            'id_requerente' => 'nullable|exists:requerentes,id',
            'id_requerente_fatura' => 'nullable|exists:requerentes,id',
            'id_imovel' => 'nullable|exists:imoveis,id',
            'id_gabinete' => 'nullable|exists:gabinetes,id',
            'id_subcontratado' => 'nullable|exists:subcontratados,id',
            'designacao' => 'nullable|string|max:500',
            'percentagem_iva' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['user_id'] = $request->user()->id;
        if (isset($validated['percentagem_iva'])) {
            $validated['percentagem_iva'] = (float) $validated['percentagem_iva'];
        } else {
            $validated['percentagem_iva'] = 23;
        }

        $idImovel = $this->resolveImovelId($request);
        if ($idImovel !== null) {
            $validated['id_imovel'] = $idImovel;
        }

        if ($validated['status'] === 'convertido') {
            $validated['data_convertido'] = Carbon::today();
        }
        if ($validated['status'] === 'faturado') {
            $validated['data_faturado'] = Carbon::today();
        }

        $orcamento = Orcamento::create($validated);

        OrcamentoHistorico::create([
            'id_orcamento' => $orcamento->id,
            'status_anterior' => null,
            'status_novo' => $orcamento->status,
            'user_id' => $request->user()->id,
        ]);

        $this->syncItens($orcamento, $request->input('itens', []));

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento criado com sucesso.');
    }

    public function show(Orcamento $orcamento): RedirectResponse
    {
        return redirect()->route('orcamentos.edit', $orcamento);
    }

    public function edit(Orcamento $orcamento): View
    {
        $orcamento->load([
            'itens.servico',
            'imovel.tipoImovel', 'imovel.distrito', 'imovel.concelho', 'imovel.freguesia',
            'historico' => fn ($q) => $q->with('user')->orderByDesc('created_at'),
        ]);

        return view('orcamentos.edit', [
            'orcamento' => $orcamento,
            'requerentes' => Requerente::orderBy('nome')->get(),
            'gabinetes' => Gabinete::orderBy('nome')->get(),
            'imoveis' => Imovel::with(['tipoImovel', 'distrito', 'concelho', 'freguesia'])->orderBy('morada')->get(),
            'subcontratados' => Subcontratado::orderBy('nome')->get(),
            'distritos' => Distrito::orderBy('nome')->get(),
            'tipo_imoveis' => TipoImovel::orderBy('tipo_imovel')->get(),
            'servicos' => Servico::where('ativo', true)->orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, Orcamento $orcamento): RedirectResponse
    {
        if ($orcamento->status === 'faturado') {
            return redirect()->route('orcamentos.edit', $orcamento)
                ->with('warning', 'Orçamento faturado não pode ser editado.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:rascunho,enviado,aceite,recusado,convertido,faturado',
            'id_requerente' => 'nullable|exists:requerentes,id',
            'id_requerente_fatura' => 'nullable|exists:requerentes,id',
            'id_imovel' => 'nullable|exists:imoveis,id',
            'id_gabinete' => 'nullable|exists:gabinetes,id',
            'id_subcontratado' => 'nullable|exists:subcontratados,id',
            'designacao' => 'nullable|string|max:500',
        ]);

        $idImovel = $this->resolveImovelId($request);
        if ($idImovel !== null) {
            $validated['id_imovel'] = $idImovel;
        }

        if ($request->input('status') === 'convertido' && ! $orcamento->data_convertido) {
            $validated['data_convertido'] = Carbon::today();
        }
        if ($request->input('status') === 'faturado' && ! $orcamento->data_faturado) {
            $validated['data_faturado'] = Carbon::today();
        }

        $statusAnterior = $orcamento->status;
        $orcamento->update($validated);

        if ($request->input('status') !== $statusAnterior) {
            OrcamentoHistorico::create([
                'id_orcamento' => $orcamento->id,
                'status_anterior' => $statusAnterior,
                'status_novo' => $orcamento->status,
                'user_id' => $request->user()->id,
            ]);
        }

        $this->syncItens($orcamento, $request->input('itens', []));

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento atualizado com sucesso.');
    }

    public function destroy(Orcamento $orcamento): RedirectResponse
    {
        $orcamento->delete();

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento eliminado.');
    }

    private function resolveImovelId(Request $request): ?int
    {
        $novo = $request->input('novo_imovel', []);
        if (! empty(array_filter($novo))) {
            $request->validate([
                'novo_imovel.morada' => 'nullable|string|max:500',
                'novo_imovel.nip' => 'nullable|string|max:50',
                'novo_imovel.codigo_postal' => 'nullable|string|max:20',
                'novo_imovel.localidade' => 'nullable|string|max:255',
                'novo_imovel.coordenadas' => 'nullable|string|max:100',
                'novo_imovel.id_tipo_imovel' => 'nullable|exists:tipo_imoveis,id',
                'novo_imovel.id_distrito' => 'nullable|exists:distritos,id_distrito',
                'novo_imovel.id_concelho' => 'nullable|exists:concelhos,id_concelho',
                'novo_imovel.id_freguesia' => 'nullable|exists:freguesias,id_freguesia',
            ]);

            $imovel = Imovel::create([
                'morada' => $novo['morada'] ?? null,
                'nip' => $novo['nip'] ?? null,
                'codigo_postal' => $novo['codigo_postal'] ?? null,
                'localidade' => $novo['localidade'] ?? null,
                'coordenadas' => $novo['coordenadas'] ?? null,
                'id_tipo_imovel' => $novo['id_tipo_imovel'] ?? null,
                'id_distrito' => $novo['id_distrito'] ?? null,
                'id_concelho' => $novo['id_concelho'] ?? null,
                'id_freguesia' => $novo['id_freguesia'] ?? null,
            ]);

            return $imovel->id;
        }

        return null;
    }

    private function syncItens(Orcamento $orcamento, array $itens): void
    {
        $orcamento->itens()->delete();

        foreach (array_filter($itens, fn ($i) => ! empty($i['descricao']) || ! empty($i['preco_base']) || ! empty($i['quantidade']) || ! empty($i['id_servico'])) as $row) {
            $orcamento->itens()->create([
                'id_servico' => ! empty($row['id_servico']) ? $row['id_servico'] : null,
                'descricao' => $row['descricao'] ?? null,
                'preco_base' => (float) ($row['preco_base'] ?? 0),
                'quantidade' => (float) ($row['quantidade'] ?? 1),
                'prazo_data' => ! empty($row['prazo_data']) ? $row['prazo_data'] : null,
                'percentagem_iva' => isset($row['percentagem_iva']) && $row['percentagem_iva'] !== '' && $row['percentagem_iva'] !== null ? (float) $row['percentagem_iva'] : null,
            ]);
        }
    }
}
