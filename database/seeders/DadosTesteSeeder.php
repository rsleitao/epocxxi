<?php

namespace Database\Seeders;

use App\Models\Gabinete;
use App\Models\Requerente;
use App\Models\Subcontratado;
use Illuminate\Database\Seeder;

class DadosTesteSeeder extends Seeder
{
    /**
     * Dados de teste: requerentes, gabinetes e subcontratados.
     */
    public function run(): void
    {
        $requerentes = [
            [
                'nif' => '123456789',
                'nome' => 'Maria Silva',
                'morada' => 'Rua das Flores, 12',
                'codigo_postal' => '1000-001',
                'email' => 'maria.silva@email.pt',
                'telefone' => '912345678',
            ],
            [
                'nif' => '987654321',
                'nome' => 'João Santos',
                'morada' => 'Avenida da Liberdade, 45',
                'codigo_postal' => '1250-096',
                'email' => 'joao.santos@email.pt',
                'telefone' => '923456789',
            ],
            [
                'nif' => '456789123',
                'nome' => 'Ana Costa',
                'morada' => 'Travessa do Comércio, 7',
                'codigo_postal' => '1100-052',
                'email' => 'ana.costa@email.pt',
                'telefone' => '934567890',
            ],
            [
                'nif' => '321654987',
                'nome' => 'Pedro Oliveira',
                'morada' => 'Largo do Município, 3',
                'codigo_postal' => '1200-109',
                'email' => 'pedro.oliveira@email.pt',
                'telefone' => '945678901',
            ],
            [
                'nif' => '654987321',
                'nome' => 'Catarina Ferreira',
                'morada' => 'Rua do Sol, 28',
                'codigo_postal' => '1300-265',
                'email' => 'catarina.ferreira@email.pt',
                'telefone' => '956789012',
            ],
        ];

        $gabinetes = [
            [
                'nif' => '500123456',
                'nome' => 'Gabinete Técnico EPOC',
                'morada' => 'Rua da Engenharia, 100',
                'codigo_postal' => '1000-200',
                'email' => 'contacto@epoc.pt',
                'telefone' => '213456789',
            ],
            [
                'nif' => '500789012',
                'nome' => 'Consultores de Energia Lda',
                'morada' => 'Avenida dos Descobrimentos, 50',
                'codigo_postal' => '1400-098',
                'email' => 'geral@consultoresenergia.pt',
                'telefone' => '218765432',
            ],
            [
                'nif' => '500345678',
                'nome' => 'Certificação & Auditoria',
                'morada' => 'Zona Industrial, Lote 15',
                'codigo_postal' => '2685-001',
                'email' => 'info@certificacao.pt',
                'telefone' => '219876543',
            ],
        ];

        $subcontratados = [
            [
                'nif' => '600111222',
                'nome' => 'Instalações Elétricas Norte',
                'morada' => 'Rua da Eletricidade, 5',
                'codigo_postal' => '4000-100',
                'email' => 'contacto@instalacoesnorte.pt',
                'telefone' => '223456789',
            ],
            [
                'nif' => '600333444',
                'nome' => 'Climatização Sul Lda',
                'morada' => 'Parque Empresarial, Edifício B',
                'codigo_postal' => '8000-200',
                'email' => 'geral@climatizacaosul.pt',
                'telefone' => '289123456',
            ],
            [
                'nif' => '600555666',
                'nome' => 'Manutenção Técnica Central',
                'morada' => 'Avenida dos Trabalhadores, 22',
                'codigo_postal' => '3000-150',
                'email' => 'info@mtecnica.pt',
                'telefone' => '239654321',
            ],
            [
                'nif' => '600777888',
                'nome' => 'Energia Renovável Lda',
                'morada' => 'Zona Verde, Lote 8',
                'codigo_postal' => '2500-300',
                'email' => 'contacto@energiarenovavel.pt',
                'telefone' => '262111222',
            ],
        ];

        foreach ($requerentes as $dados) {
            Requerente::firstOrCreate(
                ['nif' => $dados['nif']],
                $dados
            );
        }

        foreach ($gabinetes as $dados) {
            Gabinete::firstOrCreate(
                ['nif' => $dados['nif']],
                $dados
            );
        }

        foreach ($subcontratados as $dados) {
            Subcontratado::firstOrCreate(
                ['nif' => $dados['nif']],
                $dados
            );
        }
    }
}
