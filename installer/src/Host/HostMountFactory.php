<?php declare(strict_types=1);

namespace Cocotte\Host;

use Assert\Assertion;
use Cocotte\Filesystem\Filesystem;

final class HostMountFactory
{
    /**
     * @var MountsFactory
     */
    private $mountsFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(MountsFactory $mountsFactory, Filesystem $filesystem)
    {
        $this->mountsFactory = $mountsFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * @return HostMount
     * @throws \Assert\AssertionFailedException
     * @throws HostException
     */
    public function fromDocker(): HostMount
    {
        foreach ($this->mountsFactory->fromDocker()->toArray() as $mount) {
            if ('bind' === $mount['Type'] && '/host' === $mount['Destination']) {
                Assertion::true($mount['RW'], "Volume /host must be writable");
                Assertion::true(
                    $this->filesystem->isAbsolutePath($mount['Source']),
                    "Host mount source '{$mount['Source']}' is not an absolute path"
                );

                return new HostMount($mount);
            }
        }
        throw HostException::noHostMount();
    }
}