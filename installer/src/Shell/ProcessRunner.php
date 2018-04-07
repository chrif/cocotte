<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell;

use Chrif\Cocotte\Console\Style;
use Symfony\Component\Process\Process;

final class ProcessRunner
{
    /**
     * @var Style
     */
    private $style;

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    public function mustRun(Process $process)
    {
        $this->style->block($process->getCommandLine());
        $process->mustRun();
        $this->style->success($process->getOutput());
    }
}