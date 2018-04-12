<?php declare(strict_types=1);

namespace Chrif\Cocotte\Machine;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ImportableValue;
use Symfony\Component\Process\Process;

class MachineIp implements ImportableValue
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::ipv4($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromEnv(): ImportableValue
    {
        // todo use docker machine ip instead now that we connect a lot anyway?
        $process = new Process(
            [
                'docker-machine',
                '-s',
                '"${MACHINE_STORAGE_PATH}"',
                'inspect',
                "--format='{{.Driver.IPAddress}}'",
                '"${COCOTTE_MACHINE}"',
            ]
        );
        $process->mustRun();

        return new self(trim($process->getOutput()));
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    public function equals(MachineIp $ip): bool
    {
        return $this->value() === $ip->value();
    }

}