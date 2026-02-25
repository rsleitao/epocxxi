<?php

namespace App\Http\Controllers;

use App\Models\Gabinete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GabineteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Gabinete::query()->orderBy('nome');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('nome', 'like', "%{$q}%")
                    ->orWhere('nif', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $gabinetes = $query->paginate(15)->withQueryString();

        return view('gabinetes.index', compact('gabinetes'));
    }

    public function create(): View
    {
        return view('gabinetes.create');
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

        Gabinete::create($validated);

        return redirect()->route('gabinetes.index')
            ->with('success', 'Gabinete criado com sucesso.');
    }

    public function show(Gabinete $gabinete): RedirectResponse
    {
        return redirect()->route('gabinetes.edit', $gabinete);
    }

    public function edit(Gabinete $gabinete): View
    {
        return view('gabinetes.edit', compact('gabinete'));
    }

    public function update(Request $request, Gabinete $gabinete): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20',
            'morada' => 'nullable|string|max:500',
            'codigo_postal' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
        ]);

        $gabinete->update($validated);

        return redirect()->route('gabinetes.index')
            ->with('success', 'Gabinete atualizado com sucesso.');
    }

    public function destroy(Gabinete $gabinete): RedirectResponse
    {
        $gabinete->delete();

        return redirect()->route('gabinetes.index')
            ->with('success', 'Gabinete eliminado.');
    }
}
