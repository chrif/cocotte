<?php

namespace Cocotte\Test\Integration\DigitalOcean;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\DigitalOcean\HostnameCollection;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Machine\MachineIp;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\DigitalOcean\DomainActual;
use Cocotte\Test\Collaborator\DigitalOcean\NetworkingConfiguratorWithFakeMachineIpFixture;

class NetworkingConfiguratorTest extends ApplicationTestCase implements LazyEnvironment
{

    /**
     * @var Hostname
     */
    private $hostname;
    /**
     * @var HostnameCollection
     */
    private $hostnameCollection;

    public function setUp()
    {
        $this->loadEnvironment();
        $this->hostname = Hostname::parse(sprintf('app.%s.test', uniqid('cocotte-')));
        $this->hostnameCollection = HostnameCollection::fromArray([$this->hostname]);

        self::assertFalse(
            DomainActual::get($this->container())->service()->exists($this->hostname)
        );
    }

    public function tearDown()
    {
        // clean up domain for next test run because command does not remove domains
        $domain = DomainActual::get($this->container())->service();
        $domain->delete($this->hostname);
    }

    public function test_it_creates_and_removes_domain_record()
    {
        $fixture1 = new NetworkingConfiguratorWithFakeMachineIpFixture(
            $this->container(),
            new MachineIp('127.0.0.1')
        );

        self::assertFalse($fixture1->domainApi()->exists($this->hostname));

        // command should create domain, domain record for root, and domain record for sub-domain
        $fixture1->configurator()->configure($this->hostnameCollection);
        self::assertTrue($fixture1->domainApi()->exists($this->hostname));
        self::assertTrue($fixture1->domainRecordApi()->exists($this->hostname));
        self::assertTrue($fixture1->domainRecordApi()->exists($this->hostname->toRoot()));
        self::assertTrue($fixture1->domainRecordApi()->isUpToDate($this->hostname));
        unset($fixture1);

        // assert it updates ip
        $fixture2 = new NetworkingConfiguratorWithFakeMachineIpFixture(
            $this->container(),
            new MachineIp('127.0.0.2')
        );
        self::assertFalse($fixture2->domainRecordApi()->isUpToDate($this->hostname));
        $fixture2->configurator()->configure($this->hostnameCollection);
        self::assertTrue($fixture2->domainRecordApi()->isUpToDate($this->hostname));

        // command should remove domain record for sub-domain, but not domain and domain record for root
        $fixture2->configurator()->configure($this->hostnameCollection, true);
        self::assertFalse($fixture2->domainRecordApi()->exists($this->hostname));
        self::assertTrue($fixture2->domainRecordApi()->exists($this->hostname->toRoot()));
        self::assertTrue($fixture2->domainApi()->exists($this->hostname));

        // it does not error when removing a domain already removed
        $fixture2->style()->clear();
        $fixture2->configurator()->configure($this->hostnameCollection, true);
        self::assertSame(
            "Configuring networking for all the hostnames supplied: {$this->hostname}\n".
            "Configuring {$this->hostname}\n".
            "Domain record '{$this->hostname}' was already removed\n",
            $fixture2->style()->output
        );
    }

    public function test_it_handles_root_record_correctly()
    {
        $fixture = new NetworkingConfiguratorWithFakeMachineIpFixture(
            $this->container(),
            new MachineIp('127.0.0.1')
        );

        $root = $this->hostname->toRoot();
        $hostnameCollection = new HostnameCollection($root);

        self::assertFalse($fixture->domainApi()->exists($root));

        // command should create domain and domain record for root
        $fixture->configurator()->configure($hostnameCollection);
        self::assertTrue($fixture->domainApi()->exists($root));
        self::assertTrue($fixture->domainRecordApi()->exists($root));
        self::assertTrue($fixture->domainRecordApi()->isUpToDate($root));

        // command should remove domain record for root
        $fixture->configurator()->configure($hostnameCollection, true);
        self::assertFalse($fixture->domainRecordApi()->exists($root));
        self::assertTrue($fixture->domainApi()->exists($root));
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
        ];
    }

}
