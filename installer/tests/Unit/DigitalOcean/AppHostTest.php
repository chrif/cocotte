<?php

namespace Cocotte\Tests\DigitalOcean;

use Cocotte\DigitalOcean\Hostname;
use DigitalOceanV2\Entity\DomainRecord;
use PHPUnit\Framework\TestCase;

class AppHostTest extends TestCase
{

    public function test_domain()
    {
        $hostname = Hostname::parse("foo.bar.org");
        self::assertSame("bar.org", $hostname->domainName());

        $hostname = Hostname::parse("bar.org");
        self::assertSame("bar.org", $hostname->domainName());

        $hostname = Hostname::parse("www.bar.org");
        self::assertSame("bar.org", $hostname->domainName());
    }

    public function test_sub_domain()
    {
        $hostname = Hostname::parse("foo.bar.org");
        self::assertSame("foo", $hostname->recordName());

        $hostname = Hostname::parse("bar.org");
        self::assertSame(Hostname::DIGITAL_OCEAN_ROOT_RECORD, $hostname->recordName());

        $hostname = Hostname::parse("www.bar.org");
        self::assertSame("www", $hostname->recordName());
    }

    public function test_value()
    {
        $hostname = Hostname::parse("foo.bar.org");
        self::assertSame("foo.bar.org", $hostname->toString());

        $hostname = Hostname::parse("bar.org");
        self::assertSame("bar.org", $hostname->toString());

        $hostname = Hostname::parse("www.bar.org");
        self::assertSame("www.bar.org", $hostname->toString());
    }

    public function test_match_domain_record()
    {
        $hostname = Hostname::parse("foo.bar.org");
        self::assertTrue($hostname->matchDomainRecord(new DomainRecord(['name' => "foo"])));

        $hostname = Hostname::parse("bar.org");
        self::assertTrue($hostname->matchDomainRecord(new DomainRecord(['name' => Hostname::DIGITAL_OCEAN_ROOT_RECORD])));

        $hostname = Hostname::parse("www.bar.org");
        self::assertTrue($hostname->matchDomainRecord(new DomainRecord(['name' => "www"])));

        $hostname = Hostname::parse("www.bar.org");
        self::assertFalse($hostname->matchDomainRecord(new DomainRecord(['name' => "foo"])));
    }

    public function test_to_root()
    {
        $hostname = Hostname::parse("foo.bar.org");
        self::assertEquals(Hostname::parse("bar.org"), $hostname->toRoot());

        $hostname = Hostname::parse("bar.org");
        self::assertEquals(Hostname::parse("bar.org"), $hostname->toRoot());

        $hostname = Hostname::parse("foo.org");
        self::assertNotEquals(Hostname::parse("bar.org"), $hostname->toRoot());
    }

    public function test_is_root()
    {
        $hostname = Hostname::parse("foo.bar.org");
        self::assertFalse($hostname->isRoot());

        $hostname = Hostname::parse("bar.org");
        self::assertTrue($hostname->isRoot());
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
        $hostname = Hostname::fromString("@.bar.org");
        self::assertTrue($hostname->isRoot());
        self::assertSame("@.bar.org", $hostname->rawValue());
    }

    public function test_to_local()
    {
        $hostname = Hostname::parse("foo.bar.org");
        $local = $hostname->toLocal();
        self::assertSame('foo.bar.local', $local->toString());

        $hostname = Hostname::parse("bar.org");
        $local = $hostname->toLocal();
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
