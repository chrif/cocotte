<?php declare(strict_types=1);

namespace Cocotte\Shell\EnvironmentSubstitution;

use Cocotte\Console\Style;
use Cocotte\Filesystem\Filesystem;
use Cocotte\Finder\Finder;
use Cocotte\Shell\ProcessRunner;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class InPlaceSubstitution implements SubstitutionStrategy
{
    /**
     * @var Finder
     */
    private $finder;

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
        Finder $finder,
        Style $style,
        ProcessRunner $processRunner,
        Filesystem $filesystem
    ) {
        $this->finder = $finder;
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->filesystem = $filesystem;
    }

    public function substitute(Process $envSubstProcess)
    {
        /** @var SplFileInfo $file */
        foreach ($this->finder->files() as $file) {
            $this->style->verbose('In-place substitution of environment in '.$file->getRealPath());

            $envSubstProcess->setInput($file->getContents());
            $this->processRunner->mustRun($envSubstProcess);

            $this->filesystem->dumpFile($file->getRealPath(), $envSubstProcess->getOutput());
        }
    }
}