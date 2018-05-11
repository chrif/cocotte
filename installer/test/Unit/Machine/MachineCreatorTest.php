<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\Machine;

use Cocotte\Test\Collaborator\Machine\MachineCreatorDouble;
use Cocotte\Test\Collaborator\Machine\MachineStateDouble;
use PHPUnit\Framework\TestCase;

final class MachineCreatorTest extends TestCase
{

    public function test_machine_does_not_exist_exception()
    {
        $creator = ($builder = MachineCreatorDouble::create($this)->builder())
            ->setMachineState($state = MachineStateDouble::create($this)->machineExistsMock())
            ->newInstance();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            "Error: a machine named {$builder->machineName()} already exists. Remove it or choose a ".
            "different machine name. Run the uninstall command to remove it completely."
        );

        $creator->create();
    }
}
