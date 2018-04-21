<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell\EnvironmentSubstitution;

use Assert\Assertion;
use Symfony\Component\Process\Process;

final class EnvironmentSubstitution
{

    /**
     * @var string[]
     */
    private $restrictions;

    /**
     * @var array
     */
    private $exports;

    public function __construct(array $restrictions, array $exports)
    {
        $this->restrictions = $restrictions;
        $this->exports = $exports;
    }

    public static function withDefaults()
    {
        return new self([], []);
    }

    public static function formatEnvFile(array $lines): string
    {
        $lines[] = "";

        Assertion::allString($lines);

        return implode(
            "\n",
            $lines
        );
    }

    public function restrict(array $restrictions)
    {
        return new self($restrictions, $this->exports);
    }

    public function export(array $exports)
    {
        return new self($this->restrictions, $exports);
    }

    public function substitute(SubstitutionStrategy $substitutionStrategy)
    {
        $command = ['envsubst'];
        if ($this->restrictions) {
            $command[] = implode(',', $this->shellFormat($this->restrictions));
        }
        $substitutionStrategy->substitute(new Process($command, null, $this->exports));
    }

    private function shellFormat(array $keys = [])
    {
        $shellFormat = [];
        foreach ($keys as $key) {
            $shellFormat[] = '${'.$key.'}';
        }

        return $shellFormat;
    }

}