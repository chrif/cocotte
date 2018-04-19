<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell;

use Chrif\Cocotte\Console\Style;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ProcessRunner
{
    /**
     * @var Style
     */
    private $style;

    /**
     * @var ProcessHelper
     */
    private $processHelper;
    /**
     * @var ProgressBar
     */
    private $progressBar;

    public function __construct(Style $style, ProcessHelper $processHelper, ProgressBar $progressBar)
    {
        $this->style = $style;
        $this->processHelper = $processHelper;
        $this->progressBar = $progressBar;
    }

    public function mustRun(Process $process)
    {
        $useProgress = !$this->style->isVerbose();
        if ($useProgress) {
            $this->progressBar->start();
        }

        $this->processHelper->run(
            $this->style,
            $process,
            null,
            function () use ($useProgress) {
                if ($useProgress) {
                    $this->progressBar->advance();
                }
            },
            OutputInterface::VERBOSITY_VERBOSE
        );

        if ($useProgress) {
            $this->progressBar->finish();
            $this->progressBar->clear();
        }

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}