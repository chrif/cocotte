<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Assert\Assertion;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Environment\LazyLoadAware;
use Cocotte\Filesystem\Filesystem;
use Cocotte\Shell\Env;

class MachineStoragePath implements LazyEnvironmentValue, LazyLoadAware
{
    const MACHINE_STORAGE_PATH = 'MACHINE_STORAGE_PATH';
    const INPUT_OPTION = 'machine-storage-path';

    /**
     * @var string
     */
    private $value;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(string $value, Filesystem $filesystem)
    {
        Assertion::notEmpty($value, "The machine storage path is empty.");
        Assertion::regex(
            $value,
            '/^[^$"\']+$/',
            "The machine storage path '{$value}' contains dollar signs, single quotes or double quotes."
        );
        Assertion::true(
            $filesystem->isAbsolutePath($value),
            "Machine storage path '{$value}' is not an absolute path"
        );
        $this->filesystem = $filesystem;
        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function onLazyLoad(Env $env): void
    {
        $env->put(self::MACHINE_STORAGE_PATH, $this->toString());
        $this->symLink();
    }

    /**
     * This is crooked but it serves our purpose:
     * Create a path on installer identical to the storage path on host.
     * Because docker machine stores an absolute path in its config files and
     * we want it to work outside of the installer afterwards.
     * This solution is preferred to editing the json config files after machine creation and
     * every time docker machine would rewrite it.
     *
     * @throws \Exception
     */
    private function symLink()
    {
        $pathOnHostFilesystem = $this->toString();
        $pathOnInstaller = "/host/machine";

        if ($pathOnInstaller !== $pathOnHostFilesystem) {

            if (!$this->filesystem->exists($pathOnHostFilesystem)) {

                if (is_dir($pathOnInstaller) && !is_dir("$pathOnInstaller/certs")) {
                    throw new \Exception(
                        "Error: Tried to create a directory named 'machine' in the directory from where you ".
                        "executed Cocotte but it already exists and it is not a valid docker machine storage path."
                    );
                }
                $this->filesystem->mkdir("$pathOnInstaller/certs");
                $this->filesystem->symlink($pathOnInstaller, $pathOnHostFilesystem);
            } elseif (
                !is_link($pathOnHostFilesystem) ||
                $this->filesystem->readlink($pathOnHostFilesystem) !== $pathOnInstaller
            ) {
                throw new \Exception(
                    "Error: cannot symlink '$pathOnInstaller' to '$pathOnHostFilesystem' because it is a real ".
                    "path on Cocotte filesystem. Start Cocotte from a different directory on your computer. One ".
                    "that does not exist in the Filesystem Hierarchy Standard of a UNIX-like operating system\n"
                );
            }
        }
    }
}