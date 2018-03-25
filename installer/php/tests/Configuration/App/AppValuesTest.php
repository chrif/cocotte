<?php

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\CocotteConfiguration;
use PHPUnit\Framework\TestCase;

class AppValuesTest extends TestCase
{

    public function testName()
    {
        $values = new AppValues(AppName::fromString('foo'), AppHostCollection::fixture());
        self::assertSame('foo', $values->name()->value());
    }

    public function testHosts()
    {
        $values = new AppValues(AppName::fixture(), AppHostCollection::fromString("foo.bar.org"));
        self::assertCount(1, $values->hosts());
        self::assertSame('foo.bar.org', $values->hosts()[0]->value());
    }

    public function testFromArray()
    {
        $values = AppValues::fromArray(
            [
                AppName::NAME => 'foo',
                AppHostCollection::HOSTS => 'foo.bar.org',
            ]
        );

        self::assertInstanceOf(AppValues::class, $values);
    }

    public function testFromRoot()
    {
        $configuration = CocotteConfiguration::fixture();

        $values = AppValues::fromRoot($configuration);

        self::assertInstanceOf(AppValues::class, $values);

        self::assertEquals($configuration->value()['app'], $configuration->value()['app']);
    }
}