<?php

namespace App\Services\PersonParser\Fields;

use Illuminate\Support\Str;

class LastName extends AbstractField
{
    public static function canAccept(string $token): bool
    {
        return filled($token) &&
            !Title::canAccept($token) &&
            Str::match('/^[A-Za-z\-\'\s]+$/', $token);
    }
}

