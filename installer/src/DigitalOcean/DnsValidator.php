<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Assert\Assertion;
use Cocotte\Shell\Env;
use Iodev\Whois\DomainInfo;
use Iodev\Whois\Whois;

class DnsValidator
{
    const SKIP_DNS_VALIDATION = 'SKIP_DNS_VALIDATION';
    /**
     * @var Whois
     */
    private $whois;
    /**
     * @var Env
     */
    private $env;

    public function __construct(Whois $whois, Env $env)
    {
        $this->whois = $whois;
        $this->env = $env;
    }

    public function validateNameServers(Hostname $hostname)
    {
        $info = $this->whois->loadDomainInfo($hostname->domainName());
        if (!$info instanceof DomainInfo) {
            throw new \Exception("Could not load domain info of $hostname");
        }
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
            $nameServer = Hostname::parse($nameServer);
            Assertion::regex(
                $nameServer->toString(),
                "/ns[0-9]\.digitalocean\.com/",
                "'{$nameServer->toString()}' is not a Digital Ocean's name server in the ".
                "form of ns[0-9].digitalocean.com"
            );
        }
    }

    public function validateHost(Hostname $hostname)
    {
        if ($this->env->get(self::SKIP_DNS_VALIDATION)) {
            return;
        }

        try {
            $this->validateNameServers($hostname->toRoot());
        } catch (\Throwable $domainException) {
            $message[] = "Failed to validate name servers for '$hostname':";
            $message[] = $domainException->getMessage();
            throw new \Exception(implode("\n", $message));
        }
    }
}
