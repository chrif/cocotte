<?php declare(strict_types=1);

namespace Cocotte\Test\Integration\DigitalOcean;

use Cocotte\DigitalOcean\Hostname;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\DigitalOcean\DnsValidatorActual;

final class DnsValidatorTest extends ApplicationTestCase
{

    public function test_it_does_not_fail_with_fsockopen_unable_to_connect_for_rocks_domains()
    {
        $validator = DnsValidatorActual::get($this->container())->service();
        $this->expectExceptionMessage(
            "jean.ns.cloudflare.com' is a domain with more than 3 levels."
        );
        $validator->validateHost(Hostname::parse('huemor.rocks'));
    }
}
