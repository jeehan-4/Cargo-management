<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Default values used until an admin saves the settings page. */
    public const DEFAULTS = [
        'company_name'        => 'شركة إدارة الشحنات',
        'company_phone'       => '',
        'company_address'     => '',
        'default_origin_port' => 'ميناء الحديدة',
        'report_footer'       => '',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = static::where('key', $key)->value('value');
        if ($value !== null && $value !== '') {
            return $value;
        }
        return $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** All current settings merged over defaults. */
    public static function allSettings(): array
    {
        $stored = static::pluck('value', 'key')->all();
        $merged = self::DEFAULTS;
        foreach ($stored as $key => $value) {
            if (array_key_exists($key, $merged) && $value !== null && $value !== '') {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}