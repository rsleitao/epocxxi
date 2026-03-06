<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key. Uses cache for 1 hour.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = 'setting.' . $key;

        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $row = static::find($key);

            return $row !== null ? $row->value : $default;
        });
    }

    /**
     * Set a setting value. Clears cache for this key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget('setting.' . $key);
    }
}
