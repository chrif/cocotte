<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Host;

use Cocotte\Host\Mounts;
use Cocotte\Host\MountsFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class MountsFactoryDouble
{
    /**
     * @var TestCase
     */
    private $testCase;

    private function __construct()
    {
    }

    public static function create(TestCase $testCase): self
    {
        $double = new self();
        $double->testCase = $testCase;

        return $double;
    }

    /**
     * @param Mounts $mounts
     * @return MockObject|MountsFactory
     */
    public function withMounts(Mounts $mounts): MockObject
    {
        ($mock = $this->mock())
            ->method('fromDocker')
            ->willReturn($mounts);

        return $mock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(MountsFactory::class)
            ->getMock();
    }

}