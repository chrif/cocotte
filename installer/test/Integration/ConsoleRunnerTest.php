<?php declare(strict_types=1);

namespace Cocotte\Test\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class ConsoleRunnerTest extends TestCase
{

    public function test_it_runs()
    {
        $process = Process::fromShellCommandline(__DIR__.'/../../bin/console');
        $process->run();
        self::assertTrue($process->isSuccessful());
    }
}
