<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\Machine;

use Cocotte\Machine\MachineStoragePath;
use Cocotte\Test\Collaborator\Filesystem\FilesystemDouble;
use Cocotte\Test\Collaborator\Shell\FakeEnv;
use PHPUnit\Framework\TestCase;

final class MachineStoragePathTest extends TestCase
{

    public function test_no_sym_link_needed()
    {
        $machineStoragePath = new MachineStoragePath(
            "/host/machine",
            FilesystemDouble::create($this)->expectNoCallToWriteMethods()
        );

        $machineStoragePath->onLazyLoad(new FakeEnv());
    }

    public function test_it_fails_if_machine_dir_exists_on_host_but_is_not_a_docker_machine_dir()
    {
        $filesystem = FilesystemDouble::create($this)->mock();
        $filesystem->expects(self::once())->method('isAbsolutePath')->with('/foo/bar')->willReturn(true);
        $filesystem->expects(self::at(1))->method('exists')->with('/foo/bar')->willReturn(false);
        $filesystem->expects(self::at(2))->method('exists')->with('/host/machine')->willReturn(true);
        $filesystem->expects(self::at(3))->method('exists')->with('/host/machine/certs')->willReturn(false);

        $machineStoragePath = new MachineStoragePath('/foo/bar', $filesystem);

        $this->expectExceptionMessage(
            "Error: Tried to create a directory named 'machine' in the directory from where you ".
            "executed Cocotte but it already exists and it is not a valid docker machine storage path."
        );
        $machineStoragePath->onLazyLoad(new FakeEnv());
    }

    public function test_it_fails_if_path_already_exists()
    {
        self::assertFalse(is_link('/etc'));
        $filesystem = FilesystemDouble::create($this)->expectNoCallToWriteMethods();

        $machineStoragePath = new MachineStoragePath('/etc', $filesystem);

        $this->expectExceptionMessage(
            "Error: cannot symlink '/host/machine' to '/etc' because it is a real ".
            "path on Cocotte filesystem. Start Cocotte from a different directory on your computer. One ".
            "that does not exist in the Cocotte filesystem.\n"
        );
        $machineStoragePath->onLazyLoad(new FakeEnv());
    }

    public function test_it_fails_if_path_is_a_symlink_which_does_not_resolve_to_host_machine()
    {
        $filesystem = FilesystemDouble::create($this)->mock();
        $filesystem->expects(self::once())->method('isAbsolutePath')->with("/Users/tom/cocotte")->willReturn(true);
        $filesystem->expects(self::once())->method('exists')->with("/Users/tom/cocotte")->willReturn(true);
        $filesystem->expects(self::once())->method('isLink')->with("/Users/tom/cocotte")->willReturn(true);
        $filesystem->expects(self::once())->method('readlink')->with("/Users/tom/cocotte")->willReturn('/foo/bar');

        $machineStoragePath = new MachineStoragePath("/Users/tom/cocotte", $filesystem);

        $this->expectExceptionMessage(
            "Error: Cannot symlink '/host/machine' to '/Users/tom/cocotte' because ".
            "it is already symlink which resolves to '/foo/bar'."
        );
        $machineStoragePath->onLazyLoad(new FakeEnv());
    }

}
