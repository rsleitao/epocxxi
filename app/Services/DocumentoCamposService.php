<?php

namespace App\Services;

/**
 * Define os placeholders disponíveis por tipo de documento.
 * No template o utilizador usa ${chave} (ex: ${designacao}).
 */
class DocumentoCamposService
{
    public static function getCamposPorSlug(string $slug): array
    {
        return match ($slug) {
            'orcamento' => self::camposOrcamento(),
            default => [],
        };
    }

    /**
     * @return array<string, string> [ 'chave' => 'Descrição para o utilizador' ]
     */
    private static function camposOrcamento(): array
    {
        return [
            'designacao' => 'Designação do orçamento',
            'numero' => 'Número do orçamento (formato YYNNNN, ex.: 250001)',
            'data' => 'Data do orçamento (criação)',
            'requerente_nome' => 'Nome do requerente',
            'requerente_fatura_nome' => 'Nome a quem faturar',
            'gabinete_nome' => 'Gabinete',
            'imovel_tipo' => 'Tipo de imóvel',
            'imovel_morada' => 'Morada do imóvel',
            'imovel_morada_completa' => 'Morada - Código postal Localidade (ex.: E.N 3 - Casal da Igreja - 2200-709 Mouriscas - Abrantes)',
            'imovel_nip' => 'NIP do imóvel',
            'imovel_codigo_postal' => 'Código postal do imóvel',
            'imovel_localidade' => 'Localidade do imóvel',
            'imovel_distrito' => 'Distrito do imóvel',
            'imovel_concelho' => 'Concelho do imóvel',
            'imovel_freguesia' => 'Freguesia do imóvel',
            'imovel_coordenadas' => 'Coordenadas do imóvel',
            'servicos_especialidades' => 'Nomes dos serviços do orçamento em texto: 1 serviço → "X"; 2 → "X e Y"; 3+ → "X, Y e Z"',
            'servicos_codigos' => 'Códigos dos serviços em texto: 1 → "ITED"; 2 → "ITED e AVAC"; 3+ → "ITED, AVAC e X"',
            'subtotal' => 'Subtotal (valor sem IVA)',
            'valor_iva' => 'Valor do IVA',
            'total' => 'Total com IVA',
            'percentagem_iva' => 'Percentagem de IVA aplicada',
            'linhas_tabela' => 'Todas as linhas em texto (uma por linha), se não usar tabela dinâmica',
            'linha_servico' => '(Tabela dinâmica) Nome do serviço — use uma linha de tabela com ${linha_servico}, ${linha_codigo}, ${linha_descricao}, etc.; a linha é repetida pelo número de itens',
            'linha_codigo' => '(Tabela dinâmica) Código do serviço (ex.: ITED)',
            'linha_descricao' => '(Tabela dinâmica) Descrição do item',
            'linha_quantidade' => '(Tabela dinâmica) Quantidade',
            'linha_preco' => '(Tabela dinâmica) Preço unitário',
            'linha_iva' => '(Tabela dinâmica) Valor IVA da linha',
            'linha_total' => '(Tabela dinâmica) Total da linha',
            'linha_servico_lic' => '(Tabela Licenciamento) Nome do serviço — use uma linha com ${linha_servico_lic}, ${linha_codigo_lic}, ${linha_descricao_lic}, ${linha_preco_lic}, etc.',
            'linha_codigo_lic' => '(Tabela Licenciamento) Código do serviço',
            'linha_descricao_lic' => '(Tabela Licenciamento) Descrição (quebras de linha preservadas)',
            'linha_preco_lic' => '(Tabela Licenciamento) Preço',
            'linha_quantidade_lic' => '(Tabela Licenciamento) Quantidade',
            'linha_iva_lic' => '(Tabela Licenciamento) IVA',
            'linha_total_lic' => '(Tabela Licenciamento) Total',
            'linha_servico_exec' => '(Tabela Execução) Nome do serviço — use uma linha com ${linha_servico_exec}, ${linha_codigo_exec}, ${linha_descricao_exec}, ${linha_preco_exec}, etc.',
            'linha_codigo_exec' => '(Tabela Execução) Código do serviço',
            'linha_descricao_exec' => '(Tabela Execução) Descrição (quebras de linha preservadas)',
            'linha_preco_exec' => '(Tabela Execução) Preço',
            'linha_quantidade_exec' => '(Tabela Execução) Quantidade',
            'linha_iva_exec' => '(Tabela Execução) IVA',
            'linha_total_exec' => '(Tabela Execução) Total',
        ];
    }

    /**
     * Formato do placeholder para mostrar ao utilizador (ex: ${designacao}).
     */
    public static function placeholder(string $chave): string
    {
        return '${' . $chave . '}';
    }
}
