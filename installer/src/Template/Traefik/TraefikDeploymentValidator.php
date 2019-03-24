<?php declare(strict_types=1);

namespace Cocotte\Template\Traefik;

use Cocotte\Acme\CertificateChecker;
use Cocotte\Machine\MachineIp;
use Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class TraefikDeploymentValidator
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var MachineIp
     */
    private $machineIp;

    /**
     * @var CertificateChecker
     */
    private $certificateChecker;

    public function __construct(
        ProcessRunner $processRunner,
        TraefikHostname $traefikHostname,
        MachineIp $machineIp,
        CertificateChecker $certificateChecker
    ) {
        $this->processRunner = $processRunner;
        $this->traefikHostname = $traefikHostname;
        $this->machineIp = $machineIp;
        $this->certificateChecker = $certificateChecker;
    }

    public function validate()
    {
        // wait for ping, try 6 times
        $process = Process::fromShellCommandline('ping-traefik 6 2> /dev/stdout');
        $this->processRunner->mustRun($process);

        // check cert if DNS is up to date
        $this->certificateChecker->check(
            $this->traefikHostname->toString(),
            $this->machineIp->toString()
        );
    }

}
