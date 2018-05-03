<?php

namespace Cocotte\Test\Integration\DigitalOcean;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\Domain;
use Cocotte\DigitalOcean\DomainRecord;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\DigitalOcean\HostnameCollection;
use Cocotte\DigitalOcean\NetworkingConfigurator;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Machine\MachineIp;
use Cocotte\Machine\MachineName;
use Cocotte\Shell\DefaultEnv;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Console\StyleDouble;

class NetworkingConfiguratorTest extends ApplicationTestCase implements LazyEnvironment
{
    /**
     * @var NetworkingConfigurator
     */
    private $configurator;
    /**
     * @var Domain
     */
    private $domainApi;

    /**
     * @var MachineIp
     */
    private $machineIp;
    /**
     * @var DomainRecord
     */
    private $domainRecordApi;

    public function setUp()
    {
        $this->loadEnvironment();

        $this->configurator = new NetworkingConfigurator(
            $this->domainRecordApi = new DomainRecord(
                $this->application()->container()->get(\DigitalOceanV2\Api\DomainRecord::class),
                $this->domainApi = new Domain(
                    $this->application()->container()->get(\DigitalOceanV2\Api\Domain::class),
                    $this->machineIp = new MachineIp('127.0.0.1')
                ),
                $this->machineIp
            ),
            $this->domainApi,
            $this->machineIp,
            StyleDouble::create($this)->mock()
        );
    }

    public function test_it_creates_and_removes_domain_record()
    {
        $hostname = Hostname::parse(MachineName::fromEnv(new DefaultEnv())->toString().'.cocotte.test');
        $hostnameCollection = HostnameCollection::fromArray([$hostname]);

        // assert it doesn't exist from a previous test
        self::assertFalse($this->domainApi->exists($hostname));

        // command should create domain, domain record for root, and domain record for sub-domain
        $this->configurator->configure($hostnameCollection);
        self::assertTrue($this->domainApi->exists($hostname));
        self::assertTrue($this->domainRecordApi->exists($hostname));
        self::assertTrue($this->domainRecordApi->exists($hostname->toRoot()));
        self::assertTrue($this->domainRecordApi->isUpToDate($hostname));

        // command should remove domain record for sub-domain, but not domain and domain record for root
        $this->configurator->configure($hostnameCollection, true);
        self::assertFalse($this->domainRecordApi->exists($hostname));
        self::assertTrue($this->domainRecordApi->exists($hostname->toRoot()));
        self::assertTrue($this->domainApi->exists($hostname));

        // clean up domain for next test run because command does not remove domains
        $this->domainApi->delete($hostname);
        self::assertFalse($this->domainApi->exists($hostname));
    }

    public function test_it_handles_root_record_correctly()
    {
        $hostname = Hostname::parse(MachineName::fromEnv(new DefaultEnv())->toString().'.test');
        $hostCollection = HostnameCollection::fromArray([$hostname]);

        // this is a root hostname
        self::assertTrue($hostname->isRoot());

        // assert it doesn't exist from a previous test
        self::assertFalse($this->domainApi->exists($hostname));

        // command should create domain and domain record for root
        $this->configurator->configure($hostCollection);
        self::assertTrue($this->domainApi->exists($hostname));
        self::assertTrue($this->domainRecordApi->exists($hostname));
        self::assertTrue($this->domainRecordApi->isUpToDate($hostname));

        // command should remove domain record for root
        $this->configurator->configure($hostCollection, true);
        self::assertFalse($this->domainRecordApi->exists($hostname));
        self::assertTrue($this->domainApi->exists($hostname));

        // clean up domain for next test run because command does not remove domains
        $this->domainApi->delete($hostname);
        self::assertFalse($this->domainApi->exists($hostname));
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
        ];
    }

}
