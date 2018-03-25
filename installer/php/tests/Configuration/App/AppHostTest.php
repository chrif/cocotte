<?php

namespace Chrif\Cocotte\Configuration\App;

use DigitalOceanV2\Entity\Domain;
use DigitalOceanV2\Entity\DomainRecord;
use PHPUnit\Framework\TestCase;

class AppHostTest extends TestCase
{

    public function testDomain()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertSame("bar.org", $host->domain());

        $host = AppHost::fromRegularSyntax("bar.org");
        self::assertSame("bar.org", $host->domain());

        $host = AppHost::fromRegularSyntax("www.bar.org");
        self::assertSame("bar.org", $host->domain());
    }

    public function testSubDomain()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertSame("foo", $host->subDomain());

        $host = AppHost::fromRegularSyntax("bar.org");
        self::assertSame(AppHost::ROOT, $host->subDomain());

        $host = AppHost::fromRegularSyntax("www.bar.org");
        self::assertSame("www", $host->subDomain());
    }

    public function testValue()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertSame("foo.bar.org", $host->value());

        $host = AppHost::fromRegularSyntax("bar.org");
        self::assertSame(AppHost::ROOT.".bar.org", $host->value());

        $host = AppHost::fromRegularSyntax("www.bar.org");
        self::assertSame("www.bar.org", $host->value());
    }

    public function testMatchDomain()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertTrue($host->matchDomain(new Domain(['name' => "bar.org"])));

        $host = AppHost::fromRegularSyntax("bar.org");
        self::assertTrue($host->matchDomain(new Domain(['name' => "bar.org"])));

        $host = AppHost::fromRegularSyntax("www.bar.org");
        self::assertTrue($host->matchDomain(new Domain(['name' => "bar.org"])));
    }

    public function testMatchDomainRecord()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => "foo"])));

        $host = AppHost::fromRegularSyntax("bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => AppHost::ROOT])));

        $host = AppHost::fromRegularSyntax("www.bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => "www"])));

        $host = AppHost::fromRegularSyntax("www.bar.org");
        self::assertFalse($host->matchDomainRecord(new DomainRecord(['name' => "foo"])));
    }

    public function testEquals()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertTrue($host->equals(AppHost::fromRegularSyntax("foo.bar.org")));

        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertFalse($host->equals(AppHost::fromRegularSyntax("www.bar.org")));
    }

    public function testToRoot()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertEquals(AppHost::fromRegularSyntax("bar.org"), $host->toRoot());

        $host = AppHost::fromRegularSyntax("bar.org");
        self::assertEquals(AppHost::fromRegularSyntax("bar.org"), $host->toRoot());

        $host = AppHost::fromRegularSyntax("foo.org");
        self::assertNotEquals(AppHost::fromRegularSyntax("bar.org"), $host->toRoot());
    }

    public function testIsRoot()
    {
        $host = AppHost::fromRegularSyntax("foo.bar.org");
        self::assertFalse($host->isRoot());

        $host = AppHost::fromRegularSyntax("bar.org");
        self::assertTrue($host->isRoot());
    }

    /**
     * @expectedException \Assert\AssertionFailedException
     * @expectedExceptionMessage List does not contain exactly "3" elements.
     */
    public function testFromDigitalOceanInvalidRootSyntax()
    {
        AppHost::fromDigitalOceanSyntax("bar.org");
    }

    public function testFromDigitalOceanSyntax()
    {
        $host = AppHost::fromDigitalOceanSyntax("@.bar.org");
        self::assertSame("@.bar.org", $host->value());
    }
}
