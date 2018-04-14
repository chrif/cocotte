<?php declare(strict_types=1);

namespace Chrif\Cocotte\Machine;

use Symfony\Component\Process\Process;

final class MachineState
{
    /**
     * @var MachineName
     */
    private $machineName;

    public function __construct(MachineName $machineName)
    {
        $this->machineName = $machineName;
    }

    public static function fromEnv()
    {
        return new self(MachineName::fromEnv());
    }

    public function exists(): bool
    {
        $process = new Process('docker-machine -s "${MACHINE_STORAGE_PATH}" ls -q "${MACHINE_NAME}"');
        $process->run();
        if ($process->isSuccessful()) {
            return $this->machineName->value() === trim($process->getOutput());
        }

        return false;
    }

    public function isRunning(): bool
    {
        $process = new Process(
            'docker-machine -s "${MACHINE_STORAGE_PATH}" ls -q --filter="state=running" "${MACHINE_NAME}"'
        );
        $process->run();
        if ($process->isSuccessful()) {
            return $this->machineName->value() === trim($process->getOutput());
        }

        return false;
    }
}
