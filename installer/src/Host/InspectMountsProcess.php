<?php declare(strict_types=1);

namespace Cocotte\Host;

use Symfony\Component\Process\Process;

class InspectMountsProcess extends Process
{
    public function __construct()
    {
        parent::__construct('docker inspect --format="{{json .Mounts}}" $HOSTNAME');
    }
}