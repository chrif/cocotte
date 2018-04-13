<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell;

use Symfony\Component\Process\Process;

final class BasicAuth
{

    public function generate(string $username, string $password): string
    {
        $process = new Process("htpasswd -bn $username $password");
        $process->mustRun();

        return trim($process->getOutput());
    }

}