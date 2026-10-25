<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Contact details of the company, editable in the admin (Website → Instellingen).
 */
class Site
{
    public const DEFAULTS = [
        'phone' => '06 29 55 40 70',
        'email' => 'info@vermeerboomtechniek.nl',
        'street' => 'Omgang 16',
        'city' => 'Wouw',
        'kvk' => null,
        'youtube' => 'https://www.youtube.com/watch?v=WZhOGdUF91s',
    ];

    public static function get(string $key): ?string
    {
        return Setting::get("site.$key", self::DEFAULTS[$key] ?? null);
    }

    /** Phone number for a tel: link, e.g. +31629554070 */
    public static function phoneLink(): string
    {
        $digits = preg_replace('/\D/', '', (string) static::get('phone'));

        return str_starts_with($digits, '0') ? '+31' . substr($digits, 1) : '+' . $digits;
    }

    public static function youtubeId(): ?string
    {
        preg_match('~(?:youtu\.be/|v=|embed/)([\w-]{11})~', (string) static::get('youtube'), $m);

        return $m[1] ?? null;
    }
}
