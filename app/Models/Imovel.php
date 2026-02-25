<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imovel extends Model
{
    protected $table = 'imoveis';

    protected $fillable = [
        'nip', 'morada', 'id_distrito', 'id_concelho', 'id_freguesia',
        'codigo_postal', 'coordenadas', 'localidade', 'id_tipo_imovel',
    ];
}
