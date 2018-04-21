<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\Acme\CertificateChecker;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Machine\MachineIp;
use Chrif\Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class TraefikDeploymentValidator
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var MachineIp
     */
    private $machineIp;

    /**
     * @var Style
     */
    private $style;
    /**
     * @var CertificateChecker
     */
    private $certificateChecker;

    public function __construct(
        ProcessRunner $processRunner,
        Filesystem $filesystem,
        TraefikHostname $traefikHostname,
        MachineIp $machineIp,
        Style $style,
        CertificateChecker $certificateChecker
    ) {
        $this->processRunner = $processRunner;
        $this->filesystem = $filesystem;
        $this->traefikHostname = $traefikHostname;
        $this->machineIp = $machineIp;
        $this->style = $style;
        $this->certificateChecker = $certificateChecker;
    }

    public function validate()
    {
        // wait for ping, try 6 times
        $process = new Process('ping-traefik 6 2> /dev/stdout');
        $this->processRunner->mustRun($process);

        // check cert if DNS is up to date
        $this->certificateChecker->check(
            $this->traefikHostname->toString(),
            $this->machineIp->toString()
        );
    }

}