<?php declare(strict_types=1);

namespace Cocotte\Template\StaticSite;

use Cocotte\Acme\CertificateChecker;
use Cocotte\Machine\MachineIp;
use Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class StaticSiteDeploymentValidator
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var StaticSiteHostname
     */
    private $staticSiteHostname;

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
        StaticSiteHostname $staticSiteHostname,
        MachineIp $machineIp,
        CertificateChecker $certificateChecker
    ) {
        $this->processRunner = $processRunner;
        $this->staticSiteHostname = $staticSiteHostname;
        $this->machineIp = $machineIp;
        $this->certificateChecker = $certificateChecker;
    }

    public function validate()
    {
        // wait for ping, try 6 times
        $process = new Process("ping-app {$this->staticSiteHostname->toString()} 6 2> /dev/stdout");
        $this->processRunner->mustRun($process);

        // check cert if DNS is up to date
        $this->certificateChecker->check(
            $this->staticSiteHostname->toString(),
            $this->machineIp->toString()
        );
    }

}