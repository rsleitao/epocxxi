<?php

namespace App\Services;

use App\Models\Template;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentoGeradorService
{
    /**
     * Gera um ficheiro a partir do template, substituindo os placeholders.
     * Suporta .docx. Retorna o caminho do ficheiro gerado (temporário).
     *
     * @param  array<string, string>  $dados  [ 'chave' => 'valor' ] para substituir ${chave}
     */
    public function gerar(Template $template, array $dados): string
    {
        if (! $template->existeFicheiro()) {
            throw new \RuntimeException('Ficheiro do template não encontrado.');
        }

        $ext = strtolower(pathinfo($template->ficheiro, PATHINFO_EXTENSION));
        if ($ext !== 'docx') {
            throw new \RuntimeException('Apenas templates Word (.docx) são suportados.');
        }

        $caminhoTemplate = $template->caminho_completo;
        if (! is_file($caminhoTemplate)) {
            throw new \RuntimeException('Ficheiro do template não encontrado: ' . $caminhoTemplate);
        }
        $caminhoTemplate = realpath($caminhoTemplate) ?: $caminhoTemplate;
        $templateProcessor = new TemplateProcessor($caminhoTemplate);

        // Converte valores com \n para quebras de linha no Word (XML)
        $dados = self::aplicarQuebrasLinhaWord($dados);

        // Tabela dinâmica: uma linha no template com ${linha_servico}, etc. é clonada N vezes (ou apagada se 0 itens)
        if (array_key_exists('linhas_tabela_rows', $dados) && is_array($dados['linhas_tabela_rows'])) {
            $rows = $dados['linhas_tabela_rows'];
            unset($dados['linhas_tabela_rows']);
            try {
                if (count($rows) === 0) {
                    $templateProcessor->deleteRow('linha_servico');
                } else {
                    $templateProcessor->cloneRowAndSetValues('linha_servico', $rows);
                }
            } catch (\Throwable) {
                // Template pode não ter esta tabela ou placeholder com markup
            }
        }

        // Tabelas separadas por tipo de trabalho: Licenciamento e Execução
        if (array_key_exists('linhas_licenciamento_rows', $dados) && is_array($dados['linhas_licenciamento_rows'])) {
            $rows = $dados['linhas_licenciamento_rows'];
            unset($dados['linhas_licenciamento_rows']);
            try {
                if (count($rows) === 0) {
                    $templateProcessor->deleteRow('linha_servico_lic');
                } else {
                    $templateProcessor->cloneRowAndSetValues('linha_servico_lic', $rows);
                }
            } catch (\Throwable) {
                // Template pode não ter secção Licenciamento
            }
        }
        if (array_key_exists('linhas_execucao_rows', $dados) && is_array($dados['linhas_execucao_rows'])) {
            $rows = $dados['linhas_execucao_rows'];
            unset($dados['linhas_execucao_rows']);
            try {
                if (count($rows) === 0) {
                    $templateProcessor->deleteRow('linha_servico_exec');
                } else {
                    $templateProcessor->cloneRowAndSetValues('linha_servico_exec', $rows);
                }
            } catch (\Throwable) {
                // Template pode não ter secção Execução
            }
        }

        foreach ($dados as $chave => $valor) {
            if (is_scalar($valor) || $valor === null) {
                $templateProcessor->setValue($chave, (string) $valor);
            }
        }

        $nomeTemp = 'template_' . uniqid() . '.docx';
        $caminhoSaida = storage_path('app/temp/' . $nomeTemp);
        if (! is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $templateProcessor->saveAs($caminhoSaida);

        return $caminhoSaida;
    }

    /**
     * Gera e devolve uma resposta de download.
     *
     * @param  array<string, string>  $dados
     */
    public function download(Template $template, array $dados, string $nomeFicheiro): StreamedResponse
    {
        $caminho = $this->gerar($template, $dados);

        return response()->streamDownload(function () use ($caminho) {
            echo file_get_contents($caminho);
            @unlink($caminho);
        }, $nomeFicheiro, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    /**
     * Converte \n em valores para quebras de linha no Word (XML <w:br/>).
     * Aplica a todos os valores escalares e aos valores dentro de arrays de linhas.
     */
    private static function aplicarQuebrasLinhaWord(array $dados): array
    {
        foreach ($dados as $chave => $valor) {
            if (is_array($valor)) {
                $dados[$chave] = array_map(function ($row) {
                    if (! is_array($row)) {
                        return $row;
                    }
                    foreach ($row as $k => $v) {
                        if (is_string($v)) {
                            $row[$k] = self::stringComQuebrasWord($v);
                        }
                    }

                    return $row;
                }, $valor);
            } elseif (is_string($valor)) {
                $dados[$chave] = self::stringComQuebrasWord($valor);
            }
        }

        return $dados;
    }

    private static function stringComQuebrasWord(string $valor): string
    {
        $valor = trim($valor);
        if ($valor === '') {
            return '';
        }
        // Qualquer tipo de quebra (\n, \r\n, \r); ignorar linhas vazias para não criar linha em branco entre cada linha
        $linhas = array_filter(preg_split('/\r\n|\r|\n/', $valor), fn (string $l): bool => $l !== '');
        if (count($linhas) <= 1) {
            return htmlspecialchars($valor, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }
        $linhas = array_map(function ($l) {
            return htmlspecialchars($l, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        }, array_values($linhas));

        return implode('</w:t><w:br/><w:t>', $linhas);
    }
}
