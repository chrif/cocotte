<?php

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\Configuration\AppHost;
use DigitalOceanV2\Entity\DomainRecord;
use PHPUnit\Framework\TestCase;

class AppHostTest extends TestCase
{

    public function testDomain()
    {
        $host = AppHost::parse("foo.bar.org");
        self::assertSame("bar.org", $host->domainName());

        $host = AppHost::parse("bar.org");
        self::assertSame("bar.org", $host->domainName());

        $host = AppHost::parse("www.bar.org");
        self::assertSame("bar.org", $host->domainName());
    }

    public function testSubDomain()
    {
        $host = AppHost::parse("foo.bar.org");
        self::assertSame("foo", $host->recordName());

        $host = AppHost::parse("bar.org");
        self::assertSame(AppHost::DIGITAL_OCEAN_ROOT_RECORD, $host->recordName());

        $host = AppHost::parse("www.bar.org");
        self::assertSame("www", $host->recordName());
    }

    public function testValue()
    {
        $host = AppHost::parse("foo.bar.org");
        self::assertSame("foo.bar.org", $host->toString());

        $host = AppHost::parse("bar.org");
        self::assertSame("bar.org", $host->toString());

        $host = AppHost::parse("www.bar.org");
        self::assertSame("www.bar.org", $host->toString());
    }

    public function testMatchDomainRecord()
    {
        $host = AppHost::parse("foo.bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => "foo"])));

        $host = AppHost::parse("bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => AppHost::DIGITAL_OCEAN_ROOT_RECORD])));

        $host = AppHost::parse("www.bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => "www"])));

        $host = AppHost::parse("www.bar.org");
        self::assertFalse($host->matchDomainRecord(new DomainRecord(['name' => "foo"])));
    }

    public function testToRoot()
    {
        $host = AppHost::parse("foo.bar.org");
        self::assertEquals(AppHost::parse("bar.org"), $host->toRoot());

        $host = AppHost::parse("bar.org");
        self::assertEquals(AppHost::parse("bar.org"), $host->toRoot());

        $host = AppHost::parse("foo.org");
        self::assertNotEquals(AppHost::parse("bar.org"), $host->toRoot());
    }

    public function testIsRoot()
    {
        $host = AppHost::parse("foo.bar.org");
        self::assertFalse($host->isRoot());

        $host = AppHost::parse("bar.org");
        self::assertTrue($host->isRoot());
    }

    /**
     * @expectedException \Assert\AssertionFailedException
     * @expectedExceptionMessage List does not contain exactly 3 elements (2 given).
     */
    public function testFromStringInvalidRootSyntax()
    {
        AppHost::fromString("bar.org");
    }

    public function testFromStringSyntax()
    {
        $host = AppHost::fromString("@.bar.org");
        self::assertTrue($host->isRoot());
        self::assertSame("@.bar.org", $host->rawValue());
    }

    public function testToLocal()
    {
        $host = AppHost::parse("foo.bar.org");
        $local = $host->toLocal();
        self::assertSame('foo.bar.local', $local->toString());

        $host = AppHost::parse("bar.org");
        $local = $host->toLocal();
        self::assertSame('bar.local', $local->toString());
    }


}
