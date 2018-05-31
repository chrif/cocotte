<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Host;

use Cocotte\Host\InspectMountsProcess;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class InspectMountsProcessDouble
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
     * @return MockObject|InspectMountsProcess
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(InspectMountsProcess::class)
            ->getMock();
    }

}