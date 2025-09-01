<?php

namespace app\Services\PersonParser\Fields;

use Illuminate\Support\Str;

class FirstName extends AbstractField
{
    public static function canAccept(string $token): bool
    {
        // First names are typically capitalised words
        // Not a title and not empty
        return filled($token) && 
               !Title::canAccept($token) && 
               Str::match('/^[A-Za-z\-\']+$/', $token);
    }
}
