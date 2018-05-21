<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

use Cocotte\Console\InteractionOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class InteractionOperatorDouble
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
     * @return MockObject|InteractionOperator
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(InteractionOperator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}