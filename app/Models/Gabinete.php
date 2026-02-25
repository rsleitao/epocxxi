<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gabinete extends Model
{
    protected $table = 'gabinetes';

    protected $fillable = [
        'nif', 'nome', 'morada', 'codigo_postal', 'email', 'telefone',
    ];
}
