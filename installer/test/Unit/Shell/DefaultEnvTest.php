<?php

namespace Cocotte\Test\Unit\Shell;

use Cocotte\Shell\DefaultEnv;
use PHPUnit\Framework\TestCase;

class DefaultEnvTest extends TestCase
{

    public function test_it_returns_default_value()
    {
        self::assertSame('foo', (new DefaultEnv())->get('bar', 'foo'));
    }
}
