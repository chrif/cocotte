<?php

namespace Cocotte\Test\Unit\DigitalOcean;

use Cocotte\DigitalOcean\Hostname;
use Cocotte\DigitalOcean\HostnameCollection;
use PHPUnit\Framework\TestCase;

class HostnameCollectionTest extends TestCase
{

    public function testConstructor()
    {
        $collection = new HostnameCollection(
            Hostname::parse("bar.org"),
            Hostname::parse("foo.bar.org"),
            Hostname::parse("www.bar.org")
        );
        self::assertCount(3, $collection);
    }

    public function testFromScalarArray()
    {
        $collection = HostnameCollection::fromScalarArray(
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
        $collection = HostnameCollection::fromString("bar.org,foo.bar.org,www.bar.org");
        self::assertCount(3, $collection);
    }

    public function testToString()
    {
        $string = "bar.org,foo.bar.org,www.bar.org";
        $collection = HostnameCollection::fromString($string);
        self::assertSame($string, (string)$collection);
    }

    public function testToLocal()
    {
        $collection = HostnameCollection::fromScalarArray(
            [
                "bar.org",
                "foo.bar.org",
                "www.bar.org",
            ]
        );
        $expected = HostnameCollection::fromScalarArray(
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
