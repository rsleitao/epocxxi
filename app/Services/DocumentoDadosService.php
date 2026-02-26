<?php

namespace App\Services;

use App\Models\Orcamento;

/**
 * Constrói o array de dados (placeholder => valor) para preencher templates
 * conforme o tipo de documento e a entidade.
 */
class DocumentoDadosService
{
    /**
     * @return array<string, string>
     */
    public static function buildData(string $slug, mixed $entidade): array
    {
        return match ($slug) {
            'orcamento' => self::dadosOrcamento($entidade),
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private static function dadosOrcamento(Orcamento $orcamento): array
    {
        $orcamento->load([
            'requerente', 'requerenteFatura', 'gabinete',
            'imovel.tipoImovel', 'imovel.distrito', 'imovel.concelho', 'imovel.freguesia',
            'itens.servico',
        ]);

        $subtotal = $orcamento->itens->sum(fn ($i) => (float) $i->preco_base * (float) ($i->quantidade ?? 1));
        $ivaPct = (float) ($orcamento->percentagem_iva ?? 23);
        $valorIva = round($subtotal * ($ivaPct / 100), 2);
        $total = $subtotal + $valorIva;

        $linhasTexto = [];
        $linhasTabelaRows = [];
        foreach ($orcamento->itens as $item) {
            $valorLinha = (float) $item->preco_base * (float) ($item->quantidade ?? 1);
            $pctIva = (float) ($item->percentagem_iva ?? $orcamento->percentagem_iva ?? 23);
            $ivaLinha = round($valorLinha * ($pctIva / 100), 2);
            $totalLinha = $valorLinha + $ivaLinha;
            $linhasTexto[] = sprintf(
                "%s | %s | %s | %s | %s € | %s €",
                $item->servico?->nome ?? 'Serviço ocasional',
                $item->descricao ?? '—',
                $item->quantidade ?? '1',
                number_format((float) $item->preco_base, 2, ',', ' '),
                number_format($ivaLinha, 2, ',', ' '),
                number_format($totalLinha, 2, ',', ' ')
            );
            $descricaoLinha = $item->servico?->descricao ?? $item->descricao ?? '—';
            $linhasTabelaRows[] = [
                'linha_servico' => $item->servico?->nome ?? 'Serviço ocasional',
                'linha_codigo' => $item->servico?->codigo ?? '—',
                'linha_descricao' => is_string($descricaoLinha) ? $descricaoLinha : '—',
                'linha_quantidade' => (string) ($item->quantidade ?? '1'),
                'linha_preco' => number_format((float) $item->preco_base, 2, ',', ' '),
                'linha_iva' => number_format($ivaLinha, 2, ',', ' '),
                'linha_total' => number_format($totalLinha, 2, ',', ' '),
            ];
        }

        $itensLic = $orcamento->itens->filter(fn ($i) => strtolower(trim($i->servico?->tipo_trabalho ?? '')) === 'licenciamento');
        $itensExec = $orcamento->itens->filter(fn ($i) => in_array(strtolower(trim($i->servico?->tipo_trabalho ?? '')), ['execução', 'execucao'], true));
        $linhasLicenciamentoRows = self::buildLinhasPorTipo($itensLic, $orcamento, 'lic');
        $linhasExecucaoRows = self::buildLinhasPorTipo($itensExec, $orcamento, 'exec');

        $nomesServicos = $orcamento->itens->map(fn ($i) => $i->servico?->nome ?? 'Serviço ocasional')->unique()->values()->all();
        $servicosEspecialidades = self::formatarListaE($nomesServicos);
        $codigosServicos = $orcamento->itens->map(fn ($i) => $i->servico?->codigo ?? '—')->unique()->filter(fn ($c) => $c !== '—')->values()->all();
        $servicos_codigos = self::formatarListaE($codigosServicos);

        $imovel = $orcamento->imovel;

        return [
            'designacao' => $orcamento->designacao ?? '—',
            'numero' => $orcamento->numero ?? (string) $orcamento->id,
            'data' => $orcamento->created_at?->format('d/m/Y') ?? '—',
            'requerente_nome' => $orcamento->requerente?->nome ?? '—',
            'requerente_fatura_nome' => $orcamento->requerenteFatura?->nome ?? $orcamento->requerente?->nome ?? '—',
            'gabinete_nome' => $orcamento->gabinete?->nome ?? '—',
            'imovel_tipo' => $imovel?->tipoImovel?->tipo_imovel ?? '—',
            'imovel_morada' => $imovel?->morada ?? '—',
            'imovel_morada_completa' => self::moradaCompleta($imovel?->morada, $imovel?->codigo_postal, $imovel?->localidade),
            'imovel_nip' => $imovel?->nip ?? '—',
            'imovel_codigo_postal' => $imovel?->codigo_postal ?? '—',
            'imovel_localidade' => $imovel?->localidade ?? '—',
            'imovel_distrito' => $imovel?->distrito?->nome ?? '—',
            'imovel_concelho' => $imovel?->concelho?->nome ?? '—',
            'imovel_freguesia' => $imovel?->freguesia?->nome ?? '—',
            'imovel_coordenadas' => $imovel?->coordenadas ?? '—',
            'servicos_especialidades' => $servicosEspecialidades,
            'servicos_codigos' => $servicos_codigos,
            'subtotal' => number_format($subtotal, 2, ',', ' '),
            'valor_iva' => number_format($valorIva, 2, ',', ' '),
            'total' => number_format($total, 2, ',', ' '),
            'percentagem_iva' => number_format($ivaPct, 0),
            'linhas_tabela' => implode("\n", $linhasTexto),
            'linhas_tabela_rows' => $linhasTabelaRows,
            'linhas_licenciamento_rows' => $linhasLicenciamentoRows,
            'linhas_execucao_rows' => $linhasExecucaoRows,
        ];
    }

    /**
     * Constrói array de linhas para cloneRowAndSetValues (Licenciamento ou Execução).
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\OrcamentoItem>  $itens
     * @return array<int, array<string, string>>
     */
    private static function buildLinhasPorTipo($itens, Orcamento $orcamento, string $suffix): array
    {
        $rows = [];
        foreach ($itens as $item) {
            $valorLinha = (float) $item->preco_base * (float) ($item->quantidade ?? 1);
            $pctIva = (float) ($item->percentagem_iva ?? $orcamento->percentagem_iva ?? 23);
            $ivaLinha = round($valorLinha * ($pctIva / 100), 2);
            $totalLinha = $valorLinha + $ivaLinha;
            $descricao = $item->servico?->descricao ?? $item->descricao ?? '—';
            $rows[] = [
                'linha_servico_' . $suffix => $item->servico?->nome ?? 'Serviço ocasional',
                'linha_codigo_' . $suffix => $item->servico?->codigo ?? '—',
                'linha_descricao_' . $suffix => is_string($descricao) ? $descricao : '—',
                'linha_preco_' . $suffix => number_format((float) $item->preco_base, 2, ',', ' '),
                'linha_quantidade_' . $suffix => (string) ($item->quantidade ?? '1'),
                'linha_iva_' . $suffix => number_format($ivaLinha, 2, ',', ' '),
                'linha_total_' . $suffix => number_format($totalLinha, 2, ',', ' '),
            ];
        }

        return $rows;
    }

    /**
     * Formata lista em português: 1 item → "A"; 2 → "A e B"; 3+ → "A, B e C".
     *
     * @param  array<string>  $itens
     */
    private static function formatarListaE(array $itens): string
    {
        $itens = array_values(array_filter($itens));
        if (count($itens) === 0) {
            return '—';
        }
        if (count($itens) === 1) {
            return $itens[0];
        }
        if (count($itens) === 2) {
            return $itens[0] . ' e ' . $itens[1];
        }
        $ultimo = array_pop($itens);

        return implode(', ', $itens) . ' e ' . $ultimo;
    }

    private static function moradaCompleta(?string $morada, ?string $codigoPostal, ?string $localidade): string
    {
        $partes = array_filter([$morada, trim(($codigoPostal ?? '') . ' ' . ($localidade ?? ''))]);

        return implode(', ', $partes) ?: '—';
    }
}
