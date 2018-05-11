<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Machine;

use Cocotte\Machine\MachineName;

final class MachineNameFixture
{
    public static function fixture()
    {
        return new MachineName(uniqid());
    }
}