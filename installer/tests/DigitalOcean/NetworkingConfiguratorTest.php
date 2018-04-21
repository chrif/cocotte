<?php

namespace Chrif\Cocotte\Tests\DigitalOcean;

use Chrif\Cocotte\DigitalOcean\Hostname;
use Chrif\Cocotte\DigitalOcean\HostnameCollection;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\TestCase;

/**
 * @group functional
 */
class NetworkingConfiguratorTest extends TestCase
{

    /**
     * @var NetworkingConfigurator
     */
    private $configurator;

    public function setUp()
    {
        parent::setUp();
        $this->configurator = $this->application()->container()->get(NetworkingConfigurator::class);
    }

    public function test_it_creates_and_removes_domain_record()
    {
        $hostname = Hostname::parse($this->machineName()->toString().'.cocotte.test');
        $hostnameCollection = HostnameCollection::fromArray([$hostname]);
        $domain = $this->domainApi();
        $domainRecord = $this->domainRecordApi();

        // assert it doesn't exist from a previous test
        self::assertFalse($domain->exists($hostname));

        // command should create domain, domain record for root, and domain record for sub-domain
        $this->configurator->configure($hostnameCollection);
        self::assertTrue($domain->exists($hostname));
        self::assertTrue($domainRecord->exists($hostname));
        self::assertTrue($domainRecord->exists($hostname->toRoot()));

        // command should remove domain record for sub-domain, but not domain and domain record for root
        $this->configurator->configure($hostnameCollection, true);
        self::assertFalse($domainRecord->exists($hostname));
        self::assertTrue($domainRecord->exists($hostname->toRoot()));
        self::assertTrue($domain->exists($hostname));

        // clean up domain for next test run because command does not remove domains
        $domain->delete($hostname);
        self::assertFalse($domain->exists($hostname));
    }

    public function test_it_handles_root_record_correctly()
    {
        $hostname = Hostname::parse($this->machineName()->toString().'.test');
        $hostCollection = HostnameCollection::fromArray([$hostname]);
        $domain = $this->domainApi();
        $domainRecord = $this->domainRecordApi();

        // this is a root hostname
        self::assertTrue($hostname->isRoot());

        // assert it doesn't exist from a previous test
        self::assertFalse($domain->exists($hostname));

        // command should create domain and domain record for root
        $this->configurator->configure($hostCollection);
        self::assertTrue($domain->exists($hostname));
        self::assertTrue($domainRecord->exists($hostname));

        // command should remove domain record for root
        $this->configurator->configure($hostCollection, true);
        self::assertFalse($domainRecord->exists($hostname));
        self::assertTrue($domain->exists($hostname));

        // clean up domain for next test run because command does not remove domains
        $domain->delete($hostname);
        self::assertFalse($domain->exists($hostname));
    }
}
