<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Freguesia extends Model
{
    protected $table = 'freguesias';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $primaryKey = 'id_freguesia';

    protected $fillable = ['nome', 'id_concelho'];

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class, 'id_concelho');
    }
}
