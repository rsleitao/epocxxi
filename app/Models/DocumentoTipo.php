<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentoTipo extends Model
{
    protected $table = 'documento_tipos';

    protected $fillable = ['nome', 'slug', 'descricao'];

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class, 'id_documento_tipo');
    }
}
