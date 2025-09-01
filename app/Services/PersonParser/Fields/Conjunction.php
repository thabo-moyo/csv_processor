<?php

namespace app\Services\PersonParser\Fields;

use Illuminate\Support\Str;

class Conjunction extends AbstractField
{
    public static function canAccept(string $token): bool
    {
        return in_array(Str::lower($token), ['and', '&']);
    }

}
