<?php declare(strict_types=1);

namespace Cocotte\Acme;

use Cocotte\Console\Style;
use Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class CertificateChecker
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;
    /**
     * @var Style
     */
    private $style;

    public function __construct(ProcessRunner $processRunner, Style $style)
    {
        $this->processRunner = $processRunner;
        $this->style = $style;
    }

    public function check(string $hostname, string $expectedIp)
    {
        $ip = gethostbyname($hostname);
        if ($ip === $expectedIp) {
            $this->processRunner->mustRun(Process::fromShellCommandline(
                'if [ "${ACME_ENABLED:-true}" = true ]; then '.
                "check-certificate $hostname 6; ".
                'else echo "Skipping SSL verification"; fi'
            ));
        } else {
            $this->style->note("Skipping SSL verification because $hostname resolves to {$ip} ".
                "instead of machine ip which is $expectedIp\n".
                "You should wait for DNS to update or force $hostname to $expectedIp ".
                "in your /etc/hosts file.");
        }
    }
}
