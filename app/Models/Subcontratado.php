<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcontratado extends Model
{
    protected $table = 'subcontratados';

    protected $fillable = [
        'nif', 'nome', 'morada', 'codigo_postal', 'email', 'telefone',
    ];
}
