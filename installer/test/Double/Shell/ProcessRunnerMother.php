<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Shell;

use Cocotte\Shell\OsProcessRunner;
use Cocotte\Shell\ProcessRunner;
use Cocotte\Test\Double\Console\StyleOutputSpy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Process\Process;

final class ProcessRunnerMother
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
        $mother = new self();
        $mother->testCase = $testCase;

        return $mother;
    }

    /**
     * @return ProcessRunner|MockObject
     */
    public function mustNotRunMock(): ProcessRunner
    {
        $mockObject = $this->testCase->getMockBuilder(ProcessRunner::class)
            ->getMock();

        $mockObject->expects(TestCase::never())
            ->method('mustRun');

        return $mockObject;
    }

    /**
     * @param string $command
     * @return ProcessRunner|MockObject
     */
    public function mustRunCommandMock(string $command): ProcessRunner
    {
        $mockObject = $this->testCase->getMockBuilder(ProcessRunner::class)
            ->getMock();

        $mockObject
            ->expects(TestCase::once())
            ->method('mustRun')
            ->with(TestCase::callback(function (Process $process) use ($command) {
                TestCase::assertSame(
                    $command,
                    $process->getCommandLine()
                );

                return true;
            }));

        return $mockObject;
    }

    /**
     * @return ProcessRunner|MockObject
     */
    public function mock(): ProcessRunner
    {
        return $this->testCase->getMockBuilder(ProcessRunner::class)->getMock();
    }

    public function fixture(): ProcessRunner
    {
        return new OsProcessRunner(
            new StyleOutputSpy(),
            new ProcessHelper()
        );
    }
}