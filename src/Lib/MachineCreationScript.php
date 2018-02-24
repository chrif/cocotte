<?php

declare(strict_types=1);

namespace App\Lib;

use Symfony\Component\HttpFoundation\File\File;

class MachineCreationScript
{
    public function create(): File
    {
        $token = Environment::get()->digitalOceanToken();
        $name = Environment::get()->machineName();

        $command = <<<EOF
#!/bin/sh
set -eu
docker-machine create \
    --driver digitalocean \
    --digitalocean-access-token $token \
    --engine-opt log-driver="json-file" \
    --engine-opt log-opt="max-size=1m" \
    --engine-opt log-opt="max-file=10" \
    $name;
EOF;

        return HostFileSystem::get()->createFile('create-machine.sh', $command);
    }
}