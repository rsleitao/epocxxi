<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Imovel extends Model
{
    protected $table = 'imoveis';

    protected $fillable = [
        'nip', 'morada', 'id_distrito', 'id_concelho', 'id_freguesia',
        'codigo_postal', 'coordenadas', 'localidade', 'id_tipo_imovel',
    ];

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class, 'id_concelho');
    }

    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class, 'id_freguesia');
    }

    public function tipoImovel(): BelongsTo
    {
        return $this->belongsTo(TipoImovel::class, 'id_tipo_imovel');
    }
}
