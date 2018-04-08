<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Assert\Assertion;
use Chrif\Cocotte\Configuration\AppHost;

final class DnsValidator
{
    public function validate(AppHost $host)
    {
        $nameServers = dns_get_record($host->toString().'.', DNS_NS);
        Assertion::isArray(
            $nameServers,
            "Expected array. Got: ".var_export($nameServers, true)
        );
        Assertion::range(
            count($nameServers),
            1,
            3,
            "Expected 1 to 3 name servers. Got " . count($nameServers)
        );
        foreach ($nameServers as $nameServer) {
            $nameServer = AppHost::parse($nameServer['target']);
            Assertion::eq($nameServer->domainName(), "digitalocean.com");
            Assertion::regex($nameServer->recordName(), "/ns[123]/");
        }
    }
}
