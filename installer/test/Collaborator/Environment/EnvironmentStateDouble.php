<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Environment;

use Cocotte\Environment\EnvironmentState;
use Cocotte\Test\Collaborator\Shell\FakeEnv;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EnvironmentStateDouble
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

    public function fake(): EnvironmentState
    {
        return new EnvironmentState(new FakeEnv());
    }

    /**
     * @return MockObject|EnvironmentState
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(EnvironmentState::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
