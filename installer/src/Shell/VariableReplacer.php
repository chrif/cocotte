<?php declare(strict_types=1);

namespace Cocotte\Shell;

use Cocotte\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

final class VariableReplacer
{
    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function replace(Finder $finder, array $variables)
    {
        /** @var SplFileInfo $file */
        foreach ($finder->files() as $file) {
            foreach ($variables as $name => $value) {
                $this->processRunner->mustRun(
                    new Process(
                        [
                            'sed',
                            '-i',
                            sprintf('s/\${%s}/%s/g', $name, $value),
                            $file->getRealPath(),
                        ]
                    )
                );
            }
        }
    }
}