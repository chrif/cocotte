<?php

namespace Cocotte\Test\Unit\Template\Traefik;

use Cocotte\Template\Traefik\TraefikPassword;
use PHPUnit\Framework\TestCase;

class TraefikPasswordTest extends TestCase
{

    public function test_to_string()
    {
        self::assertSame('foo', (string)(new TraefikPassword('foo')));
    }
}
