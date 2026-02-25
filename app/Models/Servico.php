<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $table = 'servicos';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'unidade',
        'preco_base',
        'ativo',
        'tipo_trabalho',
    ];

    protected $casts = [
        'preco_base' => 'decimal:2',
        'ativo' => 'boolean',
    ];
}
