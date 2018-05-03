<?php

namespace Cocotte\Test\Unit\Host;

use Assert\AssertionFailedException;
use Cocotte\Host\HostException;
use Cocotte\Host\HostMountFactory;
use Cocotte\Host\Mounts;
use Cocotte\Test\Double\Filesystem\FilesystemDouble;
use Cocotte\Test\Double\Host\MountsFactoryDouble;
use PHPUnit\Framework\TestCase;

class HostMountFactoryTest extends TestCase
{
    public function test_it_explains_how_to_fix_no_host_mount_error()
    {
        $factory = new HostMountFactory(
            MountsFactoryDouble::create($this)->withMounts(new Mounts([])),
            FilesystemDouble::create($this)->mock()
        );

        $this->expectException(HostException::class);
        $this->expectExceptionMessage(
            "There is no writable bind mount with the destination '/host'.\nMake sure you run your command ".
            "with a volume like this:\n-v \"$(pwd)\":/host"
        );

        $factory->fromDocker();
    }

    public function test_it_guards_type()
    {
        $factory = new HostMountFactory(
            MountsFactoryDouble::create($this)->withMounts(new Mounts([
                [
                    'Type' => 'foo',
                    'Destination' => '/host',
                    'RW' => true,
                ],
            ])),
            FilesystemDouble::create($this)->mock()
        );

        $this->expectException(HostException::class);
        $this->expectExceptionMessage(HostException::noHostMount()->getMessage());

        $factory->fromDocker();
    }

    public function test_it_guards_destination()
    {
        $factory = new HostMountFactory(
            MountsFactoryDouble::create($this)->withMounts(new Mounts([
                [
                    'Type' => 'bind',
                    'Destination' => '/foo',
                    'RW' => true,
                ],
            ])),
            FilesystemDouble::create($this)->mock()
        );

        $this->expectException(HostException::class);
        $this->expectExceptionMessage(HostException::noHostMount()->getMessage());

        $factory->fromDocker();
    }

    public function test_it_guards_writable()
    {
        $factory = new HostMountFactory(
            MountsFactoryDouble::create($this)->withMounts(new Mounts([
                [
                    'Type' => 'bind',
                    'Destination' => '/host',
                    'RW' => false,
                ],
            ])),
            FilesystemDouble::create($this)->mock()
        );

        $this->expectException(AssertionFailedException::class);
        $this->expectExceptionMessage("Volume /host must be writable");

        $factory->fromDocker();
    }
}
