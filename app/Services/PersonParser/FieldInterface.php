<?php

namespace app\Services\PersonParser;

interface FieldInterface
{
    public function getValue(): string;

    public static function canAccept(string $token): bool;
}

