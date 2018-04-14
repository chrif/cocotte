<?php declare(strict_types=1);

namespace Chrif\Cocotte\Machine;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Filesystem\CocotteFilesystem;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class MachineStoragePath implements ImportableValue, ExportableValue, InputOptionValue
{
    const MACHINE_STORAGE_PATH = 'MACHINE_STORAGE_PATH';
    const INPUT_OPTION = 'machine-storage-path';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value);
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * @return ImportableValue|MachineStoragePath
     */
    public static function fromEnv(): ImportableValue
    {
        return new self(Env::get(self::MACHINE_STORAGE_PATH));
    }

    public static function toEnv($value): void
    {
        Env::put(self::MACHINE_STORAGE_PATH, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Machine Storage Path',
            Env::get(self::MACHINE_STORAGE_PATH)
        );
    }

    public static function inputOptionName(): string
    {
        return self::INPUT_OPTION;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(MachineName $key): bool
    {
        return $this->toString() === $key->toString();
    }

    /**
     * This is crooked but it serves our purpose:
     * Create a path on installer identical to the storage path on host.
     * Because docker machine stores an absolute path in its config files and
     * we want it to work outside of the installer afterwards.
     * This solution is preferred to editing the json config files after machine creation and
     * every time docker machine would rewrite it.
     *
     * @param Filesystem $filesystem
     * @throws \Exception
     */
    public function symLink(Filesystem $filesystem)
    {
        $userSuppliedMachinePath = $this->toString();
        $defaultMachinePath = "/host/machine";

        if ($defaultMachinePath !== $userSuppliedMachinePath) {

            if (!$filesystem->exists($userSuppliedMachinePath)) {

                if (is_dir($defaultMachinePath) && !is_dir("$defaultMachinePath/certs")) {
                    throw new \Exception(
                        "Error: Tried to create a directory named 'machine' in the directory from where you ".
                        "executed Cocotte but it already exists and it is not a valid docker machine storage path."
                    );
                }
                $filesystem->mkdir("$defaultMachinePath/certs");
                $filesystem->symlink($defaultMachinePath, $userSuppliedMachinePath);
            } elseif (
                !is_link($userSuppliedMachinePath) ||
                $filesystem->readlink($userSuppliedMachinePath) !== $defaultMachinePath
            ) {
                throw new \Exception(
                    "Error: cannot symlink '$defaultMachinePath' to '$userSuppliedMachinePath' because it is a real ".
                    "path on Cocotte filesystem. Start Cocotte from a different directory on your computer. One ".
                    "that does not exist in the Filesystem Hierarchy Standard of a UNIX-like operating system\n"
                );
            }
        }
    }

    public function __toString()
    {
        return $this->toString();
    }

}