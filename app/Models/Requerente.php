<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requerente extends Model
{
    protected $table = 'requerentes';

    protected $fillable = [
        'nif', 'nome', 'morada', 'codigo_postal', 'email', 'telefone',
    ];
}
