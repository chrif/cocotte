<?php declare(strict_types=1);

namespace Cocotte\Host;

use Symfony\Component\Process\Process;

class InspectMountsProcess extends Process
{
    public static function factory(): InspectMountsProcess
    {
        return self::fromShellCommandline('docker inspect --format="{{json .Mounts}}" $HOSTNAME');
    }
}
