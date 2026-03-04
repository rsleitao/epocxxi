<?php

namespace App\Http\Controllers;

use App\Models\Requerente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequerenteController extends Controller
{
    public function index(Request $request): View
    {
        if (! $request->user()->hasPermission('requerentes.view')) {
            return redirect()->route('dashboard')
                ->with('warning', 'Não tem permissão para ver Requerentes.');
        }

        $query = Requerente::query()->orderBy('nome');

        if (! $request->boolean('inativos')) {
            $query->where('ativo', true);
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('nome', 'like', "%{$q}%")
                    ->orWhere('nif', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $requerentes = $query->paginate(15)->withQueryString();

        return view('requerentes.index', compact('requerentes'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('requerentes.create'), 403);

        return view('requerentes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('requerentes.create'), 403);
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20',
            'morada' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        Requerente::create($validated);

        return redirect()->route('requerentes.index')
            ->with('success', 'Requerente criado com sucesso.');
    }

    public function show(Requerente $requerente): RedirectResponse
    {
        return redirect()->route('requerentes.edit', $requerente);
    }

    public function edit(Requerente $requerente): View
    {
        abort_unless(auth()->user()->hasPermission('requerentes.edit'), 403);
        return view('requerentes.edit', compact('requerente'));
    }

    public function update(Request $request, Requerente $requerente): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('requerentes.edit'), 403);
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20',
            'morada' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        $requerente->update($validated);

        return redirect()->route('requerentes.index')
            ->with('success', 'Requerente atualizado com sucesso.');
    }

    public function destroy(Requerente $requerente): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('requerentes.delete'), 403);

        $requerente->ativo = ! $requerente->ativo;
        $requerente->save();

        return redirect()->route('requerentes.index');
    }
}
