<?php

declare(strict_types=1);

namespace App\Lib;

use Symfony\Component\HttpFoundation\File\File;

class MachineRemovalScript
{

    public function create(): File
    {
        $name = Environment::get()->machineName();

        $command = <<<EOF
#!/bin/sh
set -eu
docker-machine rm -y $name;
EOF;

        return HostFileSystem::get()->createFile('remove-machine.sh', $command);
    }
}