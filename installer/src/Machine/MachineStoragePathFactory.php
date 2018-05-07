<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Cocotte\Filesystem\Filesystem;
use Cocotte\Host\HostMountFactory;

class MachineStoragePathFactory
{
    /**
     * @var HostMountFactory
     */
    private $hostMountFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(HostMountFactory $hostMountFactory, Filesystem $filesystem)
    {
        $this->hostMountFactory = $hostMountFactory;
        $this->filesystem = $filesystem;
    }

    public function fromEnv(): MachineStoragePath
    {
        return new MachineStoragePath(
            $this->hostMountFactory->fromDocker()->sourcePath().'/machine',
            $this->filesystem
        );
    }

}
