<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Servico::query()->orderBy('nome');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('nome', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%");
            });
        }

        $servicos = $query->paginate(15)->withQueryString();

        return view('servicos.index', compact('servicos'));
    }

    public function create(): View
    {
        return view('servicos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:2000',
            'unidade' => 'nullable|string|max:30',
            'preco_base' => 'nullable|numeric|min:0',
            'tipo_trabalho' => 'nullable|string|max:100',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        Servico::create($validated);

        return redirect()->route('servicos.index')
            ->with('success', 'Serviço criado com sucesso.');
    }

    public function show(Servico $servico): RedirectResponse
    {
        return redirect()->route('servicos.edit', $servico);
    }

    public function edit(Servico $servico): View
    {
        return view('servicos.edit', compact('servico'));
    }

    public function update(Request $request, Servico $servico): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:2000',
            'unidade' => 'nullable|string|max:30',
            'preco_base' => 'nullable|numeric|min:0',
            'tipo_trabalho' => 'nullable|string|max:100',
            'ativo' => 'boolean',
        ]);

        $validated['ativo'] = $request->boolean('ativo');

        $servico->update($validated);

        return redirect()->route('servicos.index')
            ->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Servico $servico): RedirectResponse
    {
        $servico->delete();

        return redirect()->route('servicos.index')
            ->with('success', 'Serviço eliminado.');
    }
}
