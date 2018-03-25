<?php

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\CocotteConfiguration;
use PHPUnit\Framework\TestCase;

class AppHostCollectionTest extends TestCase
{

    /**
     * @throws \Assert\AssertionFailedException
     */
    public function testConstructor()
    {
        $collection = new AppHostCollection(
            AppHost::fromRegularSyntax("bar.org"),
            AppHost::fromRegularSyntax("foo.bar.org"),
            AppHost::fromRegularSyntax("www.bar.org")
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

    public function testFromRoot()
    {
        $configuration = CocotteConfiguration::fixture();
        $collection = AppHostCollection::fromRoot($configuration);
        self::assertInstanceOf(AppHostCollection::class, $collection);
        self::assertEquals(
            AppHostCollection::fromString($configuration->value()['app']['hosts']),
            $collection
        );
    }
}
