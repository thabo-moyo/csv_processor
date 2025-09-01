<?php

namespace app\Services\PersonParser\Fields;

use Illuminate\Support\Str;

class Title extends AbstractField
{
    private static array $validTitles = [
        'Mr',
        'Mrs',
        'Miss',
        'Ms',
        'Dr',
        'Prof',
        'Mister'
    ];

    public static function canAccept(string $token): bool
    {
        $normalised = Str::rtrim($token, '.');
        return collect(self::$validTitles)->contains($normalised);
    }
}

