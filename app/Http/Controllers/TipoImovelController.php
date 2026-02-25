<?php

namespace App\Http\Controllers;

use App\Models\TipoImovel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoImovelController extends Controller
{
    public function index(Request $request): View
    {
        $query = TipoImovel::query()->orderBy('tipo_imovel');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('tipo_imovel', 'like', "%{$q}%")
                    ->orWhere('descricao', 'like', "%{$q}%");
            });
        }

        $tipoImoveis = $query->paginate(15)->withQueryString();

        return view('tipo-imoveis.index', compact('tipoImoveis'));
    }

    public function create(): View
    {
        return view('tipo-imoveis.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tipo_imovel' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:2000',
        ]);

        TipoImovel::create($validated);

        return redirect()->route('tipo-imoveis.index')
            ->with('success', 'Tipo de imóvel criado com sucesso.');
    }

    public function show(TipoImovel $tipoImovel): RedirectResponse
    {
        return redirect()->route('tipo-imoveis.edit', $tipoImovel);
    }

    public function edit(TipoImovel $tipoImovel): View
    {
        return view('tipo-imoveis.edit', compact('tipoImovel'));
    }

    public function update(Request $request, TipoImovel $tipoImovel): RedirectResponse
    {
        $validated = $request->validate([
            'tipo_imovel' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:2000',
        ]);

        $tipoImovel->update($validated);

        return redirect()->route('tipo-imoveis.index')
            ->with('success', 'Tipo de imóvel atualizado com sucesso.');
    }

    public function destroy(TipoImovel $tipoImovel): RedirectResponse
    {
        $tipoImovel->delete();

        return redirect()->route('tipo-imoveis.index')
            ->with('success', 'Tipo de imóvel eliminado.');
    }
}
