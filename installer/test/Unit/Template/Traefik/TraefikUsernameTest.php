<?php

namespace Cocotte\Test\Unit\Template\Traefik;

use Cocotte\Template\Traefik\TraefikUsername;
use PHPUnit\Framework\TestCase;

class TraefikUsernameTest extends TestCase
{

    public function test_to_string()
    {
        self::assertSame('foo', (string)(new TraefikUsername('foo')));
    }
}
