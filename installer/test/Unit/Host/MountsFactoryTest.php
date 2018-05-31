<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\Host;

use Cocotte\Host\HostException;
use Cocotte\Host\MountsFactory;
use Cocotte\Test\Collaborator\Host\InspectMountsProcessDouble;
use PHPUnit\Framework\TestCase;

final class MountsFactoryTest extends TestCase
{

    public function test_it_throws_no_socket_mount_exception_on_no_socket_mount()
    {
        $mountsFactory = new MountsFactory(
            $processMock = InspectMountsProcessDouble::create($this)->mock()
        );
        $processMock->expects(self::once())->method('isSuccessful')->willReturn(false);
        $processMock->expects(self::any())->method('getErrorOutput')->willReturn('var/run/docker.sock');
        $this->expectException(HostException::class);
        $this->expectExceptionMessage(HostException::noSocketMount('var/run/docker.sock')->getMessage());
        $mountsFactory->fromDocker();
    }

    public function test_it_throws_exception_on_other_errors()
    {
        $mountsFactory = new MountsFactory(
            $processMock = InspectMountsProcessDouble::create($this)->mock()
        );
        $processMock->expects(self::once())->method('isSuccessful')->willReturn(false);
        $processMock->expects(self::any())->method('getErrorOutput')->willReturn('foo');
        $this->expectException(HostException::class);
        $this->expectExceptionMessage((new HostException('foo'))->getMessage());
        $mountsFactory->fromDocker();
    }
}
