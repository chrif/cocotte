<?php

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\Configuration\AppHost;
use Chrif\Cocotte\Configuration\AppHostCollection;
use PHPUnit\Framework\TestCase;

class AppHostCollectionTest extends TestCase
{

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testConstructor()
    {
        $collection = new AppHostCollection(
            AppHost::parse("bar.org"),
            AppHost::parse("foo.bar.org"),
            AppHost::parse("www.bar.org")
        );
        self::assertCount(3, $collection);
    }

    public function testFromScalarArray()
    {
        $collection = AppHostCollection::fromScalarArray(
            [
                "bar.org",
                "foo.bar.org",
                "www.bar.org",
            ]
        );
        self::assertCount(3, $collection);
    }

    public function testFromString()
    {
        $collection = AppHostCollection::fromString("bar.org,foo.bar.org,www.bar.org");
        self::assertCount(3, $collection);
    }

    public function testToLocal()
    {
        $collection = AppHostCollection::fromScalarArray(
            [
                "bar.org",
                "foo.bar.org",
                "www.bar.org",
            ]
        );
        $expected = AppHostCollection::fromScalarArray(
            [
                "bar.local",
                "foo.bar.local",
                "www.bar.local",
            ]
        );
        $localCollection = $collection->toLocal();
        self::assertSame($expected->toString(), $localCollection->toString());
    }


}
