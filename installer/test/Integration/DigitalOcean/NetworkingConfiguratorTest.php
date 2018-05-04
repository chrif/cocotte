<?php

namespace Cocotte\Test\Integration\DigitalOcean;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\DigitalOcean\HostnameCollection;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Machine\MachineIp;
use Cocotte\Machine\MachineName;
use Cocotte\Shell\DefaultEnv;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\DigitalOcean\NetworkingConfiguratorWithFakeMachineIpFixture;

class NetworkingConfiguratorTest extends ApplicationTestCase implements LazyEnvironment
{

    public function setUp()
    {
        $this->loadEnvironment();
    }

    public function test_it_creates_and_removes_domain_record()
    {
        $fixture1 = new NetworkingConfiguratorWithFakeMachineIpFixture(
            $this,
            $this->container(),
            new MachineIp('127.0.0.1')
        );

        $hostname = Hostname::parse(MachineName::fromEnv(new DefaultEnv())->toString().'.cocotte.test');
        $hostnameCollection = HostnameCollection::fromArray([$hostname]);

        // assert it doesn't exist from a previous test
        self::assertFalse($fixture1->domainApi()->exists($hostname));

        // command should create domain, domain record for root, and domain record for sub-domain
        $fixture1->configurator()->configure($hostnameCollection);
        self::assertTrue($fixture1->domainApi()->exists($hostname));
        self::assertTrue($fixture1->domainRecordApi()->exists($hostname));
        self::assertTrue($fixture1->domainRecordApi()->exists($hostname->toRoot()));
        self::assertTrue($fixture1->domainRecordApi()->isUpToDate($hostname));
        unset($fixture1);

        // assert it updates ip
        $fixture2 = new NetworkingConfiguratorWithFakeMachineIpFixture(
            $this,
            $this->container(),
            new MachineIp('127.0.0.2')
        );
        self::assertFalse($fixture2->domainRecordApi()->isUpToDate($hostname));
        $fixture2->configurator()->configure($hostnameCollection);
        self::assertTrue($fixture2->domainRecordApi()->isUpToDate($hostname));

        // command should remove domain record for sub-domain, but not domain and domain record for root
        $fixture2->configurator()->configure($hostnameCollection, true);
        self::assertFalse($fixture2->domainRecordApi()->exists($hostname));
        self::assertTrue($fixture2->domainRecordApi()->exists($hostname->toRoot()));
        self::assertTrue($fixture2->domainApi()->exists($hostname));

        // clean up domain for next test run because command does not remove domains
        $fixture2->domainApi()->delete($hostname);
        self::assertFalse($fixture2->domainApi()->exists($hostname));
    }

    public function test_it_handles_root_record_correctly()
    {
        $fixture = new NetworkingConfiguratorWithFakeMachineIpFixture(
            $this,
            $this->container(),
            new MachineIp('127.0.0.1')
        );

        $hostname = Hostname::parse(MachineName::fromEnv(new DefaultEnv())->toString().'.test');
        $hostCollection = HostnameCollection::fromArray([$hostname]);

        // this is a root hostname
        self::assertTrue($hostname->isRoot());

        // assert it doesn't exist from a previous test
        self::assertFalse($fixture->domainApi()->exists($hostname));

        // command should create domain and domain record for root
        $fixture->configurator()->configure($hostCollection);
        self::assertTrue($fixture->domainApi()->exists($hostname));
        self::assertTrue($fixture->domainRecordApi()->exists($hostname));
        self::assertTrue($fixture->domainRecordApi()->isUpToDate($hostname));

        // command should remove domain record for root
        $fixture->configurator()->configure($hostCollection, true);
        self::assertFalse($fixture->domainRecordApi()->exists($hostname));
        self::assertTrue($fixture->domainApi()->exists($hostname));

        // clean up domain for next test run because command does not remove domains
        $fixture->domainApi()->delete($hostname);
        self::assertFalse($fixture->domainApi()->exists($hostname));
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
        ];
    }

}
