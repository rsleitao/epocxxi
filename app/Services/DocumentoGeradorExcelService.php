<?php

namespace App\Services;

use App\Models\Template;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DocumentoGeradorExcelService
{
    /**
     * Gera um ficheiro Excel a partir do template, substituindo os placeholders ${chave}
     * diretamente nos XML internos do .xlsx (ZIP).
     *
     * @param  array<string, mixed>  $dados
     */
    public function gerar(Template $template, array $dados): string
    {
        if (! $template->existeFicheiro()) {
            throw new \RuntimeException('Ficheiro do template não encontrado.');
        }

        $ext = strtolower(pathinfo($template->ficheiro, PATHINFO_EXTENSION));
        if ($ext !== 'xlsx') {
            throw new \RuntimeException('Apenas templates Excel (.xlsx) são suportados por este gerador.');
        }

        $caminhoTemplate = $template->caminho_completo;
        if (! is_file($caminhoTemplate)) {
            throw new \RuntimeException('Ficheiro do template não encontrado: ' . $caminhoTemplate);
        }

        // Mapa de "${chave}" => "valor"
        $placeholders = [];
        foreach ($dados as $chave => $valor) {
            if (is_scalar($valor) || $valor === null) {
                $placeholders['${' . $chave . '}'] = (string) $valor;
            }
        }

        $nomeTemp = 'template_' . uniqid() . '.xlsx';
        $caminhoSaida = storage_path('app/temp/' . $nomeTemp);
        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Copia o ficheiro original para não o alterar
        if (! copy($caminhoTemplate, $caminhoSaida)) {
            throw new \RuntimeException('Não foi possível copiar o template Excel.');
        }

        $zip = new ZipArchive();
        if ($zip->open($caminhoSaida) !== true) {
            throw new \RuntimeException('Não foi possível abrir o ficheiro Excel (ZIP).');
        }

        // Substitui placeholders em todos os ficheiros XML do .xlsx
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (! is_string($name)) {
                continue;
            }
            // Apenas ficheiros XML (sharedStrings, worksheets, etc.)
            if (! str_ends_with($name, '.xml')) {
                continue;
            }

            $conteudo = $zip->getFromIndex($i);
            if ($conteudo === false || $conteudo === '') {
                continue;
            }

            $novoConteudo = strtr($conteudo, $placeholders);
            if ($novoConteudo !== $conteudo) {
                $zip->addFromString($name, $novoConteudo);
            }
        }

        $zip->close();

        return $caminhoSaida;
    }

    /**
     * Gera e devolve uma resposta de download Excel.
     *
     * @param  array<string, mixed>  $dados
     */
    public function download(Template $template, array $dados, string $nomeFicheiro): StreamedResponse
    {
        $caminho = $this->gerar($template, $dados);

        return response()->streamDownload(function () use ($caminho) {
            echo file_get_contents($caminho);
            @unlink($caminho);
        }, $nomeFicheiro, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

