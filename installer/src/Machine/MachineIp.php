<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Shell\ProcessRunner;
use Darsyn\IP\IP;
use Symfony\Component\Process\Process;

class MachineIp implements LazyEnvironmentValue
{
    /**
     * @var IP
     */
    private $ip;

    public static function fromMachine(ProcessRunner $processRunner): self
    {
        $process = Process::fromShellCommandline(
            'docker-machine inspect '.
            '--format=\'{{.Driver.IPAddress}}\' "${MACHINE_NAME}"'
        );

        $processRunner->mustRun($process);

        $str = trim($process->getOutput());

        return self::fromIP(new IP($str));
    }

    public static function fromIP(IP $ip): self
    {
        $self = new self();
        $self->ip = $ip;

        return $self;
    }

    public function toIP(): IP
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->ip->getShortAddress();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
