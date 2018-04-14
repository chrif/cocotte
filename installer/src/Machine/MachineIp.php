<?php declare(strict_types=1);

namespace Chrif\Cocotte\Machine;

use Assert\Assertion;
use Chrif\Cocotte\Console\CocotteStyle;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Shell\ProcessRunner;
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
        $process = new Process('docker-machine inspect ' .
            '--format=\'{{.Driver.IPAddress}}\' "${MACHINE_NAME}"');

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