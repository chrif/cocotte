<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell\EnvironmentSubstitution;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class DumpFileSubstitution implements SubstitutionStrategy
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $contents;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        string $filename,
        string $contents,
        Style $style,
        ProcessRunner $processRunner,
        Filesystem $filesystem
    ) {
        $this->filename = $filename;
        $this->contents = $contents;
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->filesystem = $filesystem;
    }

    public function substitute(Process $envSubstProcess)
    {
        $this->style->writeln('Substituting environment in STDIN and dumping to '.$this->filename);

        $envSubstProcess->setInput($this->contents);
        $this->processRunner->mustRun($envSubstProcess);

        $this->filesystem->dumpFile(
            $this->filename,
            $envSubstProcess->getOutput()
        );
    }

}