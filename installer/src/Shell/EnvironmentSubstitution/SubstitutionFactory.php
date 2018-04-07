<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell\EnvironmentSubstitution;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Finder\Finder;
use Chrif\Cocotte\Shell\ProcessRunner;

final class SubstitutionFactory
{
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

    public function __construct(Style $style, ProcessRunner $processRunner, Filesystem $filesystem)
    {
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->filesystem = $filesystem;
    }

    public function dumpFile(string $filename, string $contents): DumpFileSubstitution
    {
        return new DumpFileSubstitution(
            $filename,
            $contents,
            $this->style,
            $this->processRunner,
            $this->filesystem
        );
    }

    public function inPlace(Finder $finder): InPlaceSubstitution
    {
        return new InPlaceSubstitution(
            $finder,
            $this->style,
            $this->processRunner,
            $this->filesystem
        );
    }
}