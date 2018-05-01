<?php declare(strict_types=1);

namespace Cocotte\Shell\EnvironmentSubstitution;

use Cocotte\Console\Style;
use Cocotte\Filesystem\Filesystem;
use Cocotte\Finder\Finder;
use Cocotte\Shell\ProcessRunner;

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