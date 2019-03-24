<?php declare(strict_types=1);

namespace Cocotte\Test\Integration\Shell;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Console\OutputActual;
use Cocotte\Test\Collaborator\Shell\ProcessRunnerActual;
use Cocotte\Test\TestCompilerPass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ProcessRunnerTest extends ApplicationTestCase
{

    public function testProgress()
    {
        $processRunner = ProcessRunnerActual::get($this->container())->service();
        $processRunner->run($process = Process::fromShellCommandline('true'), true);
        self::assertSame(
            '[░░░░░░░░░░░░░░░░░░░░░░░░░░░░] ',
            OutputActual::get($this->container())->getDisplay()
        );
    }

    public function testDisplayProgressText()
    {
        $processRunner = ProcessRunnerActual::get($this->container())->service();
        $processRunner->run($process = Process::fromShellCommandline('printf ok'), true);
        self::assertSame(
            "[░░░░░░░░░░░░░░░░░░░░░░░░░░░░] \n[▓░░░░░░░░░░░░░░░░░░░░░░░░░░░] ok",
            OutputActual::get($this->container())->getDisplay()
        );
    }

    public function testMustRun()
    {
        $processRunner = ProcessRunnerActual::get($this->container())->service();
        $this->expectException(ProcessFailedException::class);
        $this->expectExceptionMessage('The command "exit 1" failed.

Exit Code: 1(General error)

Working directory: /installer

Output:
================


Error Output:
================
');
        $processRunner->mustRun($process = Process::fromShellCommandline('exit 1'), true);
    }

    /**
     * @return TestCompilerPass
     */
    protected function compilerPass(): TestCompilerPass
    {
        return new TestCompilerPass(false);
    }

}
