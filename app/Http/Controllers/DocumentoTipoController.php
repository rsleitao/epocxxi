<?php

namespace App\Http\Controllers;

use App\Models\DocumentoTipo;
use App\Services\DocumentoCamposService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentoTipoController extends Controller
{
    private function ensureCrudEnabled(): ?RedirectResponse
    {
        if (! config('app.document_type_crud_enabled', true)) {
            return redirect()->route('documento-tipos.index');
        }

        return null;
    }

    public function index(Request $request): View
    {
        $query = DocumentoTipo::query()->withCount('templates')->orderBy('nome');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('nome', 'like', "%{$q}%")
                ->orWhere('slug', 'like', "%{$q}%");
        }

        $tipos = $query->paginate(15)->withQueryString();

        return view('documento-tipos.index', compact('tipos'));
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->ensureCrudEnabled()) {
            return $redirect;
        }

        return view('documento-tipos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->ensureCrudEnabled()) {
            return $redirect;
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:documento_tipos,slug|regex:/^[a-z0-9_-]+$/',
            'descricao' => 'nullable|string|max:500',
        ]);

        DocumentoTipo::create($validated);

        return redirect()->route('documento-tipos.index');
    }

    public function show(DocumentoTipo $documentoTipo): View
    {
        $documentoTipo->load('templates');
        $campos = DocumentoCamposService::getCamposPorSlug($documentoTipo->slug);

        return view('documento-tipos.show', compact('documentoTipo', 'campos'));
    }

    public function edit(DocumentoTipo $documentoTipo): View|RedirectResponse
    {
        if ($redirect = $this->ensureCrudEnabled()) {
            return $redirect;
        }

        return view('documento-tipos.edit', compact('documentoTipo'));
    }

    public function update(Request $request, DocumentoTipo $documentoTipo): RedirectResponse
    {
        if ($redirect = $this->ensureCrudEnabled()) {
            return $redirect;
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'required|string|max:100|regex:/^[a-z0-9_-]+$/|unique:documento_tipos,slug,' . $documentoTipo->id,
            'descricao' => 'nullable|string|max:500',
        ]);

        $documentoTipo->update($validated);

        return redirect()->route('documento-tipos.index');
    }

    public function destroy(DocumentoTipo $documentoTipo): RedirectResponse
    {
        if ($redirect = $this->ensureCrudEnabled()) {
            return $redirect;
        }

        if ($documentoTipo->templates()->exists()) {
            return redirect()->route('documento-tipos.index')
                ->with('error', 'Não pode eliminar: existem templates associados. Elimine-os primeiro.');
        }

        $documentoTipo->delete();

        return redirect()->route('documento-tipos.index');
    }
}
