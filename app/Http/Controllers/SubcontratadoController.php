<?php

namespace App\Http\Controllers;

use App\Models\Subcontratado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubcontratadoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subcontratado::query()->orderBy('nome');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('nome', 'like', "%{$q}%")
                    ->orWhere('nif', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $subcontratados = $query->paginate(15)->withQueryString();

        return view('subcontratados.index', compact('subcontratados'));
    }

    public function create(): View
    {
        return view('subcontratados.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20',
            'morada' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        Subcontratado::create($validated);

        return redirect()->route('subcontratados.index')
            ->with('success', 'Subcontratado criado com sucesso.');
    }

    public function show(Subcontratado $subcontratado): RedirectResponse
    {
        return redirect()->route('subcontratados.edit', $subcontratado);
    }

    public function edit(Subcontratado $subcontratado): View
    {
        return view('subcontratados.edit', compact('subcontratado'));
    }

    public function update(Request $request, Subcontratado $subcontratado): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20',
            'morada' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        $subcontratado->update($validated);

        return redirect()->route('subcontratados.index')
            ->with('success', 'Subcontratado atualizado com sucesso.');
    }

    public function destroy(Subcontratado $subcontratado): RedirectResponse
    {
        $subcontratado->delete();

        return redirect()->route('subcontratados.index')
            ->with('success', 'Subcontratado eliminado.');
    }
}
