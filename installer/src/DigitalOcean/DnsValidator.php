<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Assert\Assertion;
use Chrif\Cocotte\Configuration\AppHost;
use Iodev\Whois\Whois;

final class DnsValidator
{
    /**
     * @var Whois
     */
    private $whois;

    public function __construct(Whois $whois)
    {
        $this->whois = $whois;
    }

    public function validateNameServers(AppHost $host)
    {
        $info = $this->whois->loadDomainInfo($host->domainName());
        $nameServers = $info->getNameServers();
        Assertion::isArray(
            $nameServers,
            "Expected array. Got: ".var_export($nameServers, true)
        );
        Assertion::greaterThan(
            count($nameServers),
            0,
            "Expected 1 or more name servers. Got ".count($nameServers)
        );
        foreach ($nameServers as $nameServer) {
            $nameServer = AppHost::parse($nameServer);
            Assertion::eq($nameServer->domainName(), "digitalocean.com");
            Assertion::regex($nameServer->recordName(), "/ns\d/");
        }
    }

    public function validateHost(AppHost $host)
    {
        try {
            $this->validateNameServers($host->toRoot());
        } catch (\Exception $domainException) {
            $message[] = "Failed to validate name servers for '$host':";
            $message[] = $domainException->getMessage();
            throw new \Exception(implode("\n", $message));
        }
    }
}
