<?php

namespace Chrif\Cocotte\Tests\DigitalOcean;

use Chrif\Cocotte\DigitalOcean\AppHost;
use Chrif\Cocotte\DigitalOcean\AppHostCollection;
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
        $host = AppHost::parse($this->machineName()->value().'.cocotte.test');
        $hostCollection = AppHostCollection::fromArray([$host]);
        $domain = $this->domainApi();
        $domainRecord = $this->domainRecordApi();

        // assert it doesn't exist from a previous test
        self::assertFalse($domain->exists($host));

        // command should create domain, domain record for root, and domain record for sub-domain
        $this->configurator->configure($hostCollection);
        self::assertTrue($domain->exists($host));
        self::assertTrue($domainRecord->exists($host));
        self::assertTrue($domainRecord->exists($host->toRoot()));

        // command should remove domain record for sub-domain, but not domain and domain record for root
        $this->configurator->configure($hostCollection, true);
        self::assertFalse($domainRecord->exists($host));
        self::assertTrue($domainRecord->exists($host->toRoot()));
        self::assertTrue($domain->exists($host));

        // clean up domain for next test run because command does not remove domains
        $domain->delete($host);
        self::assertFalse($domain->exists($host));
    }

    public function test_it_handles_root_record_correctly()
    {
        $host = AppHost::parse($this->machineName()->value().'.test');
        $hostCollection = AppHostCollection::fromArray([$host]);
        $domain = $this->domainApi();
        $domainRecord = $this->domainRecordApi();

        // this is a root host
        self::assertTrue($host->isRoot());

        // assert it doesn't exist from a previous test
        self::assertFalse($domain->exists($host));

        // command should create domain and domain record for root
        $this->configurator->configure($hostCollection);
        self::assertTrue($domain->exists($host));
        self::assertTrue($domainRecord->exists($host));

        // command should remove domain record for root
        $this->configurator->configure($hostCollection, true);
        self::assertFalse($domainRecord->exists($host));
        self::assertTrue($domain->exists($host));

        // clean up domain for next test run because command does not remove domains
        $domain->delete($host);
        self::assertFalse($domain->exists($host));
    }
}
