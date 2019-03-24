<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Symfony\Component\Process\Process;

class MachineState
{
    /**
     * @var MachineName
     */
    private $machineName;

    public function __construct(MachineName $machineName)
    {
        $this->machineName = $machineName;
    }

    public function exists(): bool
    {
        $process = Process::fromShellCommandline('docker-machine ls -q --filter="name=${MACHINE_NAME}"');
        $process->run();
        if ($process->isSuccessful()) {
            return $this->machineName->toString() === trim($process->getOutput());
        }

        return false;
    }

    public function isRunning(): bool
    {
        $process = Process::fromShellCommandline(
            'docker-machine ls -q --filter="state=running" --filter="name=${MACHINE_NAME}"'
        );
        $process->run();
        if ($process->isSuccessful()) {
            return $this->machineName->toString() === trim($process->getOutput());
        }

        return false;
    }
}
