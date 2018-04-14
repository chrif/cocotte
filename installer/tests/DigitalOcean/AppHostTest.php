<?php

namespace Chrif\Cocotte\Tests\DigitalOcean;

use Chrif\Cocotte\DigitalOcean\Hostname;
use DigitalOceanV2\Entity\DomainRecord;
use PHPUnit\Framework\TestCase;

class AppHostTest extends TestCase
{

    public function test_domain()
    {
        $host = Hostname::parse("foo.bar.org");
        self::assertSame("bar.org", $host->domainName());

        $host = Hostname::parse("bar.org");
        self::assertSame("bar.org", $host->domainName());

        $host = Hostname::parse("www.bar.org");
        self::assertSame("bar.org", $host->domainName());
    }

    public function test_sub_domain()
    {
        $host = Hostname::parse("foo.bar.org");
        self::assertSame("foo", $host->recordName());

        $host = Hostname::parse("bar.org");
        self::assertSame(Hostname::DIGITAL_OCEAN_ROOT_RECORD, $host->recordName());

        $host = Hostname::parse("www.bar.org");
        self::assertSame("www", $host->recordName());
    }

    public function test_value()
    {
        $host = Hostname::parse("foo.bar.org");
        self::assertSame("foo.bar.org", $host->toString());

        $host = Hostname::parse("bar.org");
        self::assertSame("bar.org", $host->toString());

        $host = Hostname::parse("www.bar.org");
        self::assertSame("www.bar.org", $host->toString());
    }

    public function test_match_domain_record()
    {
        $host = Hostname::parse("foo.bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => "foo"])));

        $host = Hostname::parse("bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => Hostname::DIGITAL_OCEAN_ROOT_RECORD])));

        $host = Hostname::parse("www.bar.org");
        self::assertTrue($host->matchDomainRecord(new DomainRecord(['name' => "www"])));

        $host = Hostname::parse("www.bar.org");
        self::assertFalse($host->matchDomainRecord(new DomainRecord(['name' => "foo"])));
    }

    public function test_to_root()
    {
        $host = Hostname::parse("foo.bar.org");
        self::assertEquals(Hostname::parse("bar.org"), $host->toRoot());

        $host = Hostname::parse("bar.org");
        self::assertEquals(Hostname::parse("bar.org"), $host->toRoot());

        $host = Hostname::parse("foo.org");
        self::assertNotEquals(Hostname::parse("bar.org"), $host->toRoot());
    }

    public function test_is_root()
    {
        $host = Hostname::parse("foo.bar.org");
        self::assertFalse($host->isRoot());

        $host = Hostname::parse("bar.org");
        self::assertTrue($host->isRoot());
    }

    /**
     * @expectedException \Assert\AssertionFailedException
     * @expectedExceptionMessage List does not contain exactly 3 elements (2 given).
     */
    public function test_from_string_invalid_root_syntax()
    {
        Hostname::fromString("bar.org");
    }

    public function test_from_string_syntax()
    {
        $host = Hostname::fromString("@.bar.org");
        self::assertTrue($host->isRoot());
        self::assertSame("@.bar.org", $host->rawValue());
    }

    public function test_to_local()
    {
        $host = Hostname::parse("foo.bar.org");
        $local = $host->toLocal();
        self::assertSame('foo.bar.local', $local->toString());

        $host = Hostname::parse("bar.org");
        $local = $host->toLocal();
        self::assertSame('bar.local', $local->toString());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage 'org' does not have a first and second level domains
     */
    public function test_exception_on_less_than_2_levels()
    {
        Hostname::parse("org");
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage 'a.b.c.d' is a domain with more than 3 levels.
     */
    public function test_exception_on_more_than_3_levels()
    {
        Hostname::parse("a.b.c.d");
    }

}
