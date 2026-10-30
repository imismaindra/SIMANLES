<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'kunci';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kunci',
        'nilai',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::find($key);

        return $setting ? $setting->nilai : $default;
    }

    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['kunci' => $key], ['nilai' => $value]);
    }
}
