<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Shell;

use Cocotte\Shell\ProcessRunner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class ProcessRunnerDouble
{
    /**
     * @var TestCase
     */
    private $testCase;

    private function __construct()
    {
    }

    public static function create(TestCase $testCase): self
    {
        $double = new self();
        $double->testCase = $testCase;

        return $double;
    }

    /**
     * @return ProcessRunner|MockObject
     */
    public function mustNotRunMock(): ProcessRunner
    {
        ($mock = $this->mock())
            ->expects(TestCase::never())
            ->method('mustRun');

        return $mock;
    }

    /**
     * @param string $command
     * @return ProcessRunner|MockObject
     */
    public function mustRunCommandMock(string $command): ProcessRunner
    {
        ($mock = $this->mock())
            ->expects(TestCase::once())
            ->method('mustRun')
            ->with(TestCase::callback(function (Process $process) use ($command) {
                TestCase::assertSame(
                    $command,
                    $process->getCommandLine()
                );

                return true;
            }));

        return $mock;
    }

    /**
     * @return ProcessRunner|MockObject
     */
    public function mock(): ProcessRunner
    {
        return $this->testCase->getMockBuilder(ProcessRunner::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}