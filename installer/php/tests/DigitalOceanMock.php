<?php

declare(strict_types=1);

namespace Chrif\Cocotte;

use DigitalOceanV2\Api\Droplet;
use DigitalOceanV2\Api\Image;
use DigitalOceanV2\Api\Key;
use DigitalOceanV2\Api\Region;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class DigitalOceanMock
{

    /**
     * @var TestCase
     */
    private $testCase;

    /**
     * @param TestCase $testCase
     */
    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    public static function fromTestCase(TestCase $testCase)
    {
        return new self($testCase);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Droplet
     */
    public function dropletApi()
    {
        return $this->createMock(Droplet::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Region
     */
    public function regionApi()
    {
        return $this->createMock(Region::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Image
     */
    public function imageApi()
    {
        return $this->createMock(Image::class);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Key
     */
    public function keyApi()
    {
        return $this->createMock(Key::class);
    }

    /**
     * Returns a test double for the specified class.
     *
     * @param string $originalClassName
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     *
     * @throws \PHPUnit\Framework\Exception
     */
    private function createMock($originalClassName)
    {
        return $this->testCase->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }
}