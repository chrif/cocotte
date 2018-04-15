<?php declare(strict_types=1);

namespace Chrif\Cocotte\Host;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Filesystem\CocotteFilesystem;
use Chrif\Cocotte\Filesystem\Filesystem;

class HostMount implements ImportableValue
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
     * @return self|ImportableValue
     * @throws \Assert\AssertionFailedException
     * @throws \Exception
     */
    public static function fromEnv(): ImportableValue
    {
        foreach (Mounts::fromEnv()->toArray() as $mount) {
            if ('bind' === $mount['Type'] && '/host' === $mount['Destination']) {
                Assertion::true($mount['RW'], "Volume /host must be writable");

                return new self($mount, CocotteFilesystem::create());
            }
        }
        throw new \Exception("There is no writable bind mount with the destination '/host");
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