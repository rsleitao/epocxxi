<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    protected $table = 'orcamentos';

    protected $fillable = [
        'status', 'id_requerente', 'id_requerente_fatura', 'id_imovel', 'id_gabinete',
        'designacao', 'id_processo', 'data_convertido', 'data_faturado',
        'user_id', 'id_subcontratado',
    ];

    protected $casts = [
        'data_convertido' => 'date',
        'data_faturado' => 'date',
    ];
}
