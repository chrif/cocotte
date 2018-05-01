<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Shell;

use Cocotte\Shell\OsProcessRunner;
use Cocotte\Shell\ProcessRunner;
use Cocotte\Test\Double\Console\TestStyle;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProcessHelper;

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
    public function mock(): ProcessRunner
    {
        return $this->testCase->getMockBuilder(ProcessRunner::class)->getMock();
    }

    public function fixture(): ProcessRunner
    {
        return new OsProcessRunner(
            new TestStyle(),
            new ProcessHelper()
        );
    }
}