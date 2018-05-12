<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Machine;

use PHPUnit\Framework\TestCase;

final class MachineCreatorDouble
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

    public function builder(): MachineCreatorDoubleBuilder
    {
        return new MachineCreatorDoubleBuilder($this->testCase);
    }
}
