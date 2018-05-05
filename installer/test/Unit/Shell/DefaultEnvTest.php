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

    public function test_it_unset_value()
    {
        $env = new DefaultEnv();
        $env->put('foo', 'bar');
        self::assertSame('bar', $env->get('foo'));
        $env->unset('foo');
        self::assertSame(null, $env->get('foo'));
    }

}
