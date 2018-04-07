<?php declare(strict_types=1);

namespace Chrif\Cocotte\Configuration;

use Assert\Assertion;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

final class MachineIp implements EnvironmentValue
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

    public static function fromEnv(): MachineIp
    {
        $process = new Process('machine-ip');
        $process->run();
        if ($process->isSuccessful()) {
            return new self(trim($process->getOutput()));
        } else {
            $style = new SymfonyStyle(new StringInput(""), new ConsoleOutput());

            $style->warning(
                "Could not get Machine IP, defaulting to 127.0.0.1. The error was:\n".$process->getErrorOutput()
            );

            return new self('127.0.0.1');
        }
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