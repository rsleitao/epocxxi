<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\Processo;
use App\Services\DocumentoDadosService;
use App\Services\DocumentoGeradorExcelService;
use App\Services\DocumentoGeradorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ZipArchive;

class ProcessoController extends Controller
{
    public function index(Request $request): View
    {
        if (! $request->user()->hasPermission('processos.view')) {
            return redirect()->route('dashboard')
                ->with('warning', 'Não tem permissão para ver Processos.');
        }
        $query = Processo::query()
            ->withCount('orcamentos')
            ->with(['requerente', 'imovel'])
            ->orderByDesc('ano')
            ->orderByDesc('numero_sequencial');

        if ($request->filled('ano')) {
            $query->where('ano', (int) $request->input('ano'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($qry) use ($q) {
                $qry->where('designacao', 'like', "%{$q}%")
                    ->orWhereHas('requerente', fn ($r) => $r->where('nome', 'like', "%{$q}%"))
                    ->orWhereHas('imovel', fn ($i) => $i->where('morada', 'like', "%{$q}%")->orWhere('nip', 'like', "%{$q}%"));
            });
        }

        $processos = $query->paginate(15)->withQueryString();
        $anos = Processo::query()->distinct()->pluck('ano')->sort()->values();

        return view('processos.index', compact('processos', 'anos'));
    }

    public function show(Processo $processo): View
    {
        abort_unless(auth()->user()->hasPermission('processos.view'), 403);
        $processo->load([
            'requerente',
            'imovel.distrito',
            'imovel.concelho',
            'imovel.freguesia',
            'imovel.tipoImovel',
            'orcamentos' => fn ($q) => $q->with(['gabinete', 'subcontratado', 'itens']),
        ]);

        $valorFaturado = $processo->orcamentos
            ->where('status', 'faturado')
            ->sum(fn ($o) => $o->total_com_iva);

        $templatesPartesEscritas = Template::with('documentoTipo')
            ->whereHas('documentoTipo', fn ($q) => $q->where('slug', 'parteescritas'))
            ->orderBy('nome')
            ->get();

        $hasTemplatesPartesEscritas = $templatesPartesEscritas->isNotEmpty();

        // Normaliza para duas listas: Word e Excel
        $mapped = $templatesPartesEscritas->map(function (Template $t) {
            $ext = strtolower(pathinfo($t->ficheiro, PATHINFO_EXTENSION));
            return [
                'id' => $t->id,
                'nome' => $t->nome,
                'nome_original' => $t->nome_original,
                'ficheiro' => $t->ficheiro,
                'ext' => $ext,
            ];
        });

        $templatesPartesEscritasWord = $mapped->filter(fn ($t) => $t['ext'] === 'docx')->values();
        $templatesPartesEscritasExcel = $mapped->filter(fn ($t) => $t['ext'] === 'xlsx')->values();

        return view('processos.show', [
            'processo' => $processo,
            'valorFaturado' => $valorFaturado,
            'templatesPartesEscritasWord' => $templatesPartesEscritasWord,
            'templatesPartesEscritasExcel' => $templatesPartesEscritasExcel,
            'hasTemplatesPartesEscritas' => $hasTemplatesPartesEscritas,
        ]);
    }

    /**
     * Gerar documentos de Partes Escritas (Word/Excel) para um processo e devolver ZIP.
     */
    public function gerarPartesEscritas(Request $request, Processo $processo)
    {
        $validated = $request->validate([
            'templates' => 'required|array|min:1',
            'templates.*' => 'integer|exists:templates,id',
        ]);

        $templates = Template::with('documentoTipo')
            ->whereIn('id', $validated['templates'])
            ->get();

        if ($templates->isEmpty()) {
            return back()->with('error', 'Nenhum template encontrado.');
        }

        // Garante que todos são do tipo Partes Escritas
        if ($templates->contains(fn (Template $t) => $t->documentoTipo?->slug !== 'parteescritas')) {
            return back()->with('error', 'Alguns templates selecionados não são do tipo Partes Escritas.');
        }

        $dados = DocumentoDadosService::buildData('parteescritas', $processo);
        $geradorWord = app(DocumentoGeradorService::class);
        $geradorExcel = app(DocumentoGeradorExcelService::class);

        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $arquivos = [];
        foreach ($templates as $template) {
            $ext = strtolower(pathinfo($template->ficheiro, PATHINFO_EXTENSION));
            // nome dentro do ZIP: usar o nome original do ficheiro, se existir
            $baseNome = $template->nome_original ?: ($template->nome . '.' . $ext);

            try {
                if ($ext === 'docx') {
                    $caminho = $geradorWord->gerar($template, $dados);
                } elseif ($ext === 'xlsx') {
                    $caminho = $geradorExcel->gerar($template, $dados);
                } else {
                    continue;
                }

                $arquivos[] = ['path' => $caminho, 'name' => $baseNome];
            } catch (\Throwable $e) {
                // Se algum template falhar, continua com os restantes
                continue;
            }
        }

        if (count($arquivos) === 0) {
            return back()->with('error', 'Não foi possível gerar nenhum ficheiro a partir dos templates selecionados.');
        }

        $zip = new ZipArchive();
        $ref = $processo->referencia ?? $processo->id;
        $zipName = 'Proc-' . $ref . '-PE-' . now()->format('Ymd') . '.zip';
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipName;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Não foi possível criar o ficheiro ZIP.');
        }

        foreach ($arquivos as $arquivo) {
            if (is_file($arquivo['path'])) {
                $zip->addFile($arquivo['path'], $arquivo['name']);
            }
        }

        $zip->close();

        // Limpa os ficheiros temporários individuais
        foreach ($arquivos as $arquivo) {
            @unlink($arquivo['path']);
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
