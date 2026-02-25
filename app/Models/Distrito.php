<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distrito extends Model
{
    protected $table = 'distritos';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $primaryKey = 'id_distrito';

    protected $fillable = ['nome'];

    public function concelhos(): HasMany
    {
        return $this->hasMany(Concelho::class, 'id_distrito');
    }
}
