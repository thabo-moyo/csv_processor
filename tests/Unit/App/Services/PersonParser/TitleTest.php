<?php

namespace Tests\Unit\App\Services\PersonParser;

use App\Services\PersonParser\Fields\Title;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    public function test_can_create_title()
    {
        $title = new Title('Mr');
        $this->assertEquals('Mr', $title->getValue());
    }

    public function test_fails_with_wrong_title()
    {
        $this->expectException(InvalidArgumentException::class);
        new Title('should fail');
    }
}
