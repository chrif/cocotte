<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Cocotte\Console\Style;
use Darsyn\IP\IP;

final class NetworkingConfigurator
{
    /**
     * @var DomainRecord
     */
    private $domainRecord;

    /**
     * @var Domain
     */
    private $domain;

    /**
     * @var Style
     */
    private $style;

    public function __construct(DomainRecord $domainRecord, Domain $domain, Style $style)
    {
        $this->domainRecord = $domainRecord;
        $this->domain = $domain;
        $this->style = $style;
    }

    /**
     * @param HostnameCollection|Hostname[] $hostnames
     * @param IP $ip
     */
    public function configure(HostnameCollection $hostnames, IP $ip)
    {
        $this->style->veryVerbose('Configuring networking for all the hostnames supplied: '.$hostnames->toString());
        foreach ($hostnames as $host) {
            $this->style->veryVerbose('Configuring '.$host);
            $this->configureDomain($host, $ip);
        }
    }

    /**
     * @param HostnameCollection|Hostname[] $hostnames
     */
    public function remove(HostnameCollection $hostnames)
    {
        $this->style->veryVerbose('Removing networking for all the hostnames supplied: '.$hostnames->toString());
        foreach ($hostnames as $host) {
            $this->style->veryVerbose('Removing '.$host);
            $this->removeDomainRecord($host);
        }
    }

    private function configureDomain(Hostname $hostname, IP $ip): void
    {
        if (!$this->domain->exists($hostname)) {
            $this->style->verbose(
                "Domain '{$hostname->toRoot()}' does not exist. Creating it and adding ".
                "{$hostname->toRoot()} with ip {$ip->getShortAddress()}"
            );
            $this->domain->create($hostname, $ip);
        }

        $this->configureDomainRecord($hostname, $ip);
    }

    private function configureDomainRecord(Hostname $hostname, IP $ip): void
    {
        if ($this->domainRecord->exists($hostname)) {
            if (!$this->domainRecord->isUpToDate($hostname, $ip)) {
                $this->style->note(
                    "Domain record '{$hostname}' exists. Updating its ip to {$ip->getShortAddress()}"
                );
                $this->domainRecord->update($hostname, $ip);
            } else {
                $this->style->verbose("Domain record '{$hostname}' exists and its ip is up-to-date.");
            }
        } else {
            $this->style->verbose(
                "Domain record '{$hostname}' does not exist. Creating it with ip {$ip->getShortAddress()}"
            );
            $this->domainRecord->create($hostname, $ip);
        }
    }

    private function removeDomainRecord(Hostname $hostname): void
    {
        if ($this->domainRecord->exists($hostname)) {
            $this->style->verbose("Removing domain record '{$hostname}'");
            $this->domainRecord->delete($hostname);
        } else {
            $this->style->verbose("Domain record '{$hostname}' was already removed");
        }
    }

}