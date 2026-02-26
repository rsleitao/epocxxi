<?php

namespace App\Http\Controllers;

use App\Models\DocumentoTipo;
use App\Models\Orcamento;
use App\Models\Template;
use App\Services\DocumentoDadosService;
use App\Services\DocumentoGeradorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(Request $request): View
    {
        $query = Template::query()->with('documentoTipo')->orderBy('nome');

        if ($request->filled('id_documento_tipo')) {
            $query->where('id_documento_tipo', $request->input('id_documento_tipo'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where('nome', 'like', "%{$q}%");
        }

        $templates = $query->paginate(15)->withQueryString();
        $tipos = DocumentoTipo::orderBy('nome')->get();

        return view('templates.index', compact('templates', 'tipos'));
    }

    public function create(Request $request): View
    {
        $tipos = DocumentoTipo::orderBy('nome')->get();
        $tipoPreSelected = $request->filled('id_documento_tipo')
            ? DocumentoTipo::find($request->input('id_documento_tipo'))
            : null;

        return view('templates.create', compact('tipos', 'tipoPreSelected'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_documento_tipo' => 'required|exists:documento_tipos,id',
            'nome' => 'required|string|max:255',
            'ficheiro' => 'required|file|mimes:docx|max:10240',
            'is_predefinido' => 'nullable|boolean',
        ]);

        $file = $request->file('ficheiro');
        $nomeUnico = uniqid('tpl_') . '.docx';
        Storage::disk('local')->makeDirectory('templates');
        $path = $file->storeAs('templates', $nomeUnico, 'local');

        if ($validated['is_predefinido'] ?? false) {
            Template::where('id_documento_tipo', $validated['id_documento_tipo'])
                ->where('is_predefinido', true)
                ->update(['is_predefinido' => false]);
        }

        Template::create([
            'id_documento_tipo' => $validated['id_documento_tipo'],
            'nome' => $validated['nome'],
            'ficheiro' => $nomeUnico,
            'nome_original' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'is_predefinido' => (bool) ($validated['is_predefinido'] ?? false),
        ]);

        return redirect()->route('templates.index');
    }

    public function edit(Template $template): View
    {
        $template->load('documentoTipo');
        $tipos = DocumentoTipo::orderBy('nome')->get();

        return view('templates.edit', compact('template', 'tipos'));
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'ficheiro' => 'nullable|file|mimes:docx|max:10240',
            'is_predefinido' => 'nullable|boolean',
        ]);

        if ($request->hasFile('ficheiro')) {
            Storage::disk('local')->delete('templates/' . $template->ficheiro);
            $file = $request->file('ficheiro');
            $nomeUnico = uniqid('tpl_') . '.docx';
            Storage::disk('local')->makeDirectory('templates');
            $file->storeAs('templates', $nomeUnico, 'local');
            $template->ficheiro = $nomeUnico;
            $template->nome_original = $file->getClientOriginalName();
            $template->mime_type = $file->getMimeType();
        }

        if ($validated['is_predefinido'] ?? false) {
            Template::where('id_documento_tipo', $template->id_documento_tipo)
                ->where('id', '!=', $template->id)
                ->where('is_predefinido', true)
                ->update(['is_predefinido' => false]);
        }

        $template->nome = $validated['nome'];
        $template->is_predefinido = (bool) ($validated['is_predefinido'] ?? false);
        $template->save();

        return redirect()->route('templates.index');
    }

    public function destroy(Template $template): RedirectResponse
    {
        Storage::disk('local')->delete('templates/' . $template->ficheiro);
        $template->delete();

        return redirect()->route('templates.index');
    }

    /**
     * Gerar documento a partir do template para um orçamento.
     * Rota: orcamentos/{orcamento}/gerar-documento/{template}
     */
    public function gerarOrcamento(Orcamento $orcamento, Template $template): RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        if ($template->documentoTipo->slug !== 'orcamento') {
            return redirect()->route('orcamentos.edit', $orcamento)
                ->with('error', 'O template selecionado não é para orçamentos.');
        }

        if (! $template->existeFicheiro()) {
            return redirect()->route('orcamentos.edit', $orcamento)
                ->with('error', 'Ficheiro do template não encontrado.');
        }

        $dados = DocumentoDadosService::buildData('orcamento', $orcamento);
        $gerador = app(DocumentoGeradorService::class);
        $nomeFicheiro = 'orcamento-' . $orcamento->id . '-' . Str::slug($template->nome) . '.docx';

        return $gerador->download($template, $dados, $nomeFicheiro);
    }
}
