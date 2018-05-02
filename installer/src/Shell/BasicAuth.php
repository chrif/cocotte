<?php declare(strict_types=1);

namespace Cocotte\Shell;

use Symfony\Component\Process\Process;

final class BasicAuth
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function generate(string $username, string $password): string
    {
        $process = new Process(['htpasswd', '-b', '-n', $username, $password]);
        $this->processRunner->mustRun($process);

        return trim($process->getOutput());
    }

}