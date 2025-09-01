<?php

namespace app\Services\PersonParser\Fields;

use Illuminate\Support\Str;

class Initial extends AbstractField
{
    public static function canAccept(string $token): bool
    {
        // Single letter optionally followed by a period (A, A., J.)
        // used https://regex101.com/ for quick tests
        return filled($token) &&
            Str::match('/^[A-Z]\.?$/i', $token);
    }
}

