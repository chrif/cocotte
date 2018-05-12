<?php

namespace Cocotte\Test\Unit\Template\Traefik;

use Cocotte\DigitalOcean\Hostname;
use Cocotte\Template\Traefik\TraefikHostname;
use PHPUnit\Framework\TestCase;

class TraefikHostnameTest extends TestCase
{

    public function test_to_string()
    {
        self::assertSame('foo.bar.baz', (string)(new TraefikHostname(Hostname::parse('foo.bar.baz'))));
    }
}
