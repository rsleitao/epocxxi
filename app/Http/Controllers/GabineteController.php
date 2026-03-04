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
        if (! $request->user()->hasPermission('gabinetes.view')) {
            return redirect()->route('dashboard')
                ->with('warning', 'Não tem permissão para ver Gabinetes.');
        }
        $query = Gabinete::query()->orderBy('nome');

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

        $gabinetes = $query->paginate(15)->withQueryString();

        return view('gabinetes.index', compact('gabinetes'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('gabinetes.create'), 403);
        return view('gabinetes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('gabinetes.create'), 403);
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
        abort_unless(auth()->user()->hasPermission('gabinetes.edit'), 403);
        return view('gabinetes.edit', compact('gabinete'));
    }

    public function update(Request $request, Gabinete $gabinete): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('gabinetes.edit'), 403);
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
        abort_unless(auth()->user()->hasPermission('gabinetes.delete'), 403);

        $gabinete->ativo = ! $gabinete->ativo;
        $gabinete->save();

        return redirect()->route('gabinetes.index');
    }
}
