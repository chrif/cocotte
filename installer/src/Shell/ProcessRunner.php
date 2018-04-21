<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell;

use Chrif\Cocotte\Console\Style;
use Symfony\Component\Console\Helper\ProcessHelper;
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

    public function __construct(Style $style, ProcessHelper $processHelper)
    {
        $this->style = $style;
        $this->processHelper = $processHelper;
    }

    public function mustRun(Process $process, $displayProgressText = false)
    {
        $useProgress = !$this->style->isVerbose();
        $progressBar = $this->style->createProgressBar();
        $progressBar->setFormat('[%bar%] %message%');
        $progressBar->setMessage('');

        if ($useProgress) {
            $progressBar->start();
        }

        $this->processHelper->run(
            $this->style,
            $process,
            null,
            function ($type, $buffer) use ($useProgress, $displayProgressText, $progressBar) {
                if ($useProgress) {
                    if ($displayProgressText && Process::OUT === $type) {
                        $progressBar->setMessage(substr(preg_replace('#\s+#', ' ', $buffer), 0, 100));
                    }
                    $progressBar->advance();
                }
            },
            OutputInterface::VERBOSITY_VERBOSE
        );

        if ($useProgress) {
            $progressBar->finish();
            $progressBar->clear();
        }

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}