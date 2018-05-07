<?php declare(strict_types=1);

namespace Cocotte\Test\Integration\DigitalOcean;

use Cocotte\DigitalOcean\Hostname;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\DigitalOcean\DnsValidatorActual;

final class DnsValidatorTest extends ApplicationTestCase
{
    /**
     * @doesNotPerformAssertions
     *
     * @throws \Exception
     */
    public function test_it_validates_my_rocks_domain()
    {
        $validator = DnsValidatorActual::get($this->container())->service();
        $validator->validateHost(Hostname::parse('cocotte.rocks'));
    }

    public function test_it_does_not_fail_with_fsockopen_unable_to_connect_for_rocks_domains()
    {
        $validator = DnsValidatorActual::get($this->container())->service();
        $this->expectExceptionMessage(
            "Failed to validate name servers for 'huemor.rocks':\n".
            "fsockopen(): php_network_getaddresses: getaddrinfo failed: Name does not resolve"
        );
        $validator->validateHost(Hostname::parse('huemor.rocks'));
    }
}
