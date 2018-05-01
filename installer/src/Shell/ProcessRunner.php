<?php declare(strict_types=1);

namespace Cocotte\Shell;

use Symfony\Component\Process\Process;

interface ProcessRunner
{

    public function mustRun(Process $process, $displayProgressText = false);

    public function run(Process $process, $displayProgressText = false);
}
