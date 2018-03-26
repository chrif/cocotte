<?php

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Configuration\App\AppHost;
use Chrif\Cocotte\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group functional
 */
class ConfigureNetworkingFunctionalTest extends TestCase
{

    /**
     * @var ConfigureNetworking
     */
    private $command;

    public function setUp()
    {
        parent::setUp();
        $this->command = $this->application()->console()->get('cocotte:configure-networking');
    }

    public function test_it_creates_and_removes_domain_record()
    {
        $tester = new CommandTester($this->command);
        $host = AppHost::fromRegularSyntax($this->dropletName()->value().'.cocotte.test');
        $domain = $this->domainApi();
        $domainRecord = $this->domainRecordApi();

        // assert it doesn't exist from a previous test
        self::assertFalse($domain->exists($host));

        // command should create domain, domain record for root, and domain record for sub-domain
        $tester->execute(
            [
                'hosts' => $host->value(),
            ]
        );
        self::assertTrue($domain->exists($host));
        self::assertTrue($domainRecord->exists($host));
        self::assertTrue($domainRecord->exists($host->toRoot()));

        // command should remove domain record for sub-domain, but not domain and domain record for root
        $tester->execute(
            [
                'hosts' => $host->value(),
                '--remove' => true,
            ]
        );
        self::assertFalse($domainRecord->exists($host));
        self::assertTrue($domainRecord->exists($host->toRoot()));
        self::assertTrue($domain->exists($host));

        // clean up domain for next test run because command does not remove domains
        $domain->delete($host);
        self::assertFalse($domain->exists($host));
    }
}
