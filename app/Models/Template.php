<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Template extends Model
{
    protected $table = 'templates';

    protected $fillable = [
        'id_documento_tipo',
        'nome',
        'ficheiro',
        'nome_original',
        'mime_type',
        'is_predefinido',
    ];

    protected $casts = [
        'is_predefinido' => 'boolean',
    ];

    public function documentoTipo(): BelongsTo
    {
        return $this->belongsTo(DocumentoTipo::class, 'id_documento_tipo');
    }

    public function getCaminhoCompletoAttribute(): string
    {
        // Usar a mesma raiz do disco "local" (ex.: storage/app/private)
        $root = rtrim(config('filesystems.disks.local.root'), DIRECTORY_SEPARATOR);
        return $root . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->ficheiro;
    }

    public function existeFicheiro(): bool
    {
        return Storage::disk('local')->exists('templates/' . $this->ficheiro);
    }
}
