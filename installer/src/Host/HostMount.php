<?php declare(strict_types=1);

namespace Cocotte\Host;

use Assert\Assertion;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Filesystem\Filesystem;

class HostMount implements LazyEnvironmentValue
{
    /**
     * @var array
     */
    private $value;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(array $value, Filesystem $filesystem)
    {
        Assertion::keyExists($value, 'Source');
        Assertion::string($value['Source']);
        Assertion::true(
            $filesystem->isAbsolutePath($value['Source']),
            "Host mount source '{$value['Source']}' is not an absolute path"
        );

        $this->value = $value;
        $this->filesystem = $filesystem;
    }

    /**
     * Absolute path on the host filesystem
     *
     * @return string
     */
    public function sourcePath(): string
    {
        return $this->value['Source'];
    }
}