<?php

namespace app\Services\PersonParser\Fields;

use app\Services\PersonParser\FieldInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Field classes to work out the csv structure and granula conditions
 */
abstract class AbstractField implements FieldInterface
{
    protected string $value;

    public function __construct(string $value)
    {
        static::validate($value);
        $this->value = $value ? self::normalise($value) : null;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function normalise(string $value): string
    {
        return Str::title(trim($value));
    }

    public static function validate(string $token): void
    {
        if (! static::canAccept($token)) {
            throw new InvalidArgumentException();
        }
    }
}
