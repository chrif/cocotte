<?php declare(strict_types=1);

namespace Cocotte\Test\Integration;

use Cocotte\Test\ApplicationTestCase;

final class ConsoleRunnerTest extends ApplicationTestCase
{

    public function test_it_runs()
    {
        exec(__DIR__.'/../../bin/console', $out, $ret);
        self::assertSame(0, $ret);
    }
}