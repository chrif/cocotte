<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Assert\Assertion;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

class MachineIp implements LazyEnvironmentValue
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

    /**
     * @param ProcessRunner $processRunner
     * @return MachineIp
     */
    public static function fromMachine(ProcessRunner $processRunner): MachineIp
    {
        $process = new Process(
            'docker-machine inspect '.
            '--format=\'{{.Driver.IPAddress}}\' "${MACHINE_NAME}"'
        );

        $processRunner->mustRun($process);

        return new self(trim($process->getOutput()));
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}