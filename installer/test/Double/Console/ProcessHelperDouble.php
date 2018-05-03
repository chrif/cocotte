<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Console;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\ProcessHelper;

final class ProcessHelperDouble
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
     * @return MockObject|ProcessHelper
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(ProcessHelper::class)->getMock();
    }
}
