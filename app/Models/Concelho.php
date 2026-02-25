<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concelho extends Model
{
    protected $table = 'concelhos';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $primaryKey = 'id_concelho';

    protected $fillable = ['nome', 'id_distrito'];

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    public function freguesias(): HasMany
    {
        return $this->hasMany(Freguesia::class, 'id_concelho');
    }
}
