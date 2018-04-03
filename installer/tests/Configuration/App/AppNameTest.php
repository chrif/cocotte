<?php

namespace Chrif\Cocotte\Configuration\App;

use PHPUnit\Framework\TestCase;

class AppNameTest extends TestCase
{

    public function testValue()
    {
        $name = new AppName('name');
        self::assertSame('name', $name->value());
    }

    public function testEquals()
    {
        $name = new AppName('a');
        self::assertTrue($name->equals(new AppName('a')));
        self::assertFalse($name->equals(new AppName('b')));
    }

    public function testFromString()
    {
        $name = AppName::fromString('a');
        self::assertInstanceOf(AppName::class, $name);
        self::assertSame('a', $name->value());
    }

}
