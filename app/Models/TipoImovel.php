<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoImovel extends Model
{
    protected $table = 'tipo_imoveis';

    protected $fillable = [
        'tipo_imovel',
        'descricao',
    ];
}
