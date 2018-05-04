<?php

namespace Cocotte\Test\Integration\DigitalOcean;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\Domain;
use Cocotte\DigitalOcean\DomainRecord;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\DigitalOcean\HostnameCollection;
use Cocotte\DigitalOcean\NetworkingConfigurator;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Console\StyleOutputSpy;
use Cocotte\Test\Collaborator\DigitalOcean\DomainActual;
use Cocotte\Test\Collaborator\DigitalOcean\DomainRecordActual;
use Darsyn\IP\IP;

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
    /**
     * @var StyleOutputSpy
     */
    private $style;
    /**
     * @var DomainRecord
     */
    private $domainRecordApi;
    /**
     * @var NetworkingConfigurator
     */
    private $configurator;
    /**
     * @var Domain
     */
    private $domainApi;

    public function setUp()
    {
        $this->loadEnvironment();
        $this->hostname = Hostname::parse(sprintf('app.%s.test', uniqid('cocotte-')));
        $this->hostnameCollection = HostnameCollection::fromArray([$this->hostname]);
        $this->configurator = new NetworkingConfigurator(
            $this->domainRecordApi = DomainRecordActual::get($this->container())->service(),
            $this->domainApi = DomainActual::get($this->container())->service(),
            $this->style = new StyleOutputSpy()
        );

        self::assertFalse($this->domainApi->exists($this->hostname));
    }

    public function tearDown()
    {
        // clean up domain for next test run because command does not remove domains
        $this->domainApi->delete($this->hostname);
    }

    public function test_it_creates_and_removes_domain_record()
    {
        $ip = new IP('127.0.0.1');

        self::assertFalse($this->domainApi->exists($this->hostname));

        // command should create domain, domain record for root, and domain record for sub-domain
        $this->configurator->configure($this->hostnameCollection, $ip);
        self::assertTrue($this->domainApi->exists($this->hostname));
        self::assertTrue($this->domainRecordApi->exists($this->hostname));
        self::assertTrue($this->domainRecordApi->exists($this->hostname->toRoot()));
        self::assertTrue($this->domainRecordApi->isUpToDate($this->hostname, $ip));

        // assert it updates ip
        $ip2 = new IP('127.0.0.2');
        self::assertFalse($this->domainRecordApi->isUpToDate($this->hostname, $ip2));
        $this->configurator->configure($this->hostnameCollection, $ip2);
        self::assertTrue($this->domainRecordApi->isUpToDate($this->hostname, $ip2));

        // command should remove domain record for sub-domain, but not domain and domain record for root
        $this->configurator->remove($this->hostnameCollection);
        self::assertFalse($this->domainRecordApi->exists($this->hostname));
        self::assertTrue($this->domainRecordApi->exists($this->hostname->toRoot()));
        self::assertTrue($this->domainApi->exists($this->hostname));

        // it does not error when removing a domain already removed
        $this->style->clear();
        $this->configurator->remove($this->hostnameCollection);
        self::assertSame(
            "Removing networking for all the hostnames supplied: {$this->hostnameCollection}\n".
            "Removing {$this->hostname}\n".
            "Domain record '{$this->hostname}' was already removed\n",
            $this->style->output
        );
    }

    public function test_it_handles_root_record_correctly()
    {
        $ip = new IP('127.0.0.1');

        $root = $this->hostname->toRoot();
        $hostnameCollection = new HostnameCollection($root);

        self::assertFalse($this->domainApi->exists($root));

        // command should create domain and domain record for root
        $this->configurator->configure($hostnameCollection, $ip);
        self::assertTrue($this->domainApi->exists($root));
        self::assertTrue($this->domainRecordApi->exists($root));
        self::assertTrue($this->domainRecordApi->isUpToDate($root, $ip));

        // command should remove domain record for root
        $this->configurator->remove($hostnameCollection);
        self::assertFalse($this->domainRecordApi->exists($root));
        self::assertTrue($this->domainApi->exists($root));
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
        ];
    }

}
