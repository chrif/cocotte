<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Machine;

use Cocotte\Machine\MachineState;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MachineStateDouble
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
     * @return MachineState|MockObject
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(MachineState::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MachineState|MockObject
     */
    public function machineExistsMock(): MockObject
    {
        ($mock = $this->mock())
            ->method('exists')
            ->willReturn(true);

        return $mock;
    }
}