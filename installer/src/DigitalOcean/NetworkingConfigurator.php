<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Machine\MachineIp;

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
     * @var MachineIp
     */
    private $machineIp;

    /**
     * @var Style
     */
    private $style;

    public function __construct(DomainRecord $domainRecord, Domain $domain, MachineIp $machineIp, Style $style)
    {
        $this->domainRecord = $domainRecord;
        $this->domain = $domain;
        $this->machineIp = $machineIp;
        $this->style = $style;
    }

    /**
     * @param HostnameCollection|Hostname[] $hostnames
     * @param bool $remove
     */
    public function configure(HostnameCollection $hostnames, $remove = false)
    {
        $this->style->title('Configuring networking for all the hostnames supplied: ');
        $this->style->listing($hostnames->toArray());
        foreach ($hostnames as $host) {
            $this->style->section($host);
            if ($remove) {
                $this->removeDomainRecord($host);
            } else {
                $this->configureDomain($host);
            }
        }
        $this->style->ok("");
    }

    private function configureDomain(Hostname $hostname): void
    {
        if (!$this->domain->exists($hostname)) {
            $this->style->writeln(
                "Domain '{$hostname->toRoot()}' does not exist. Creating it and adding ".
                "{$hostname->toRoot()} with ip ".$this->machineIp->toString()
            );
            $this->domain->create($hostname);
        }

        $this->configureDomainRecord($hostname);
    }

    private function configureDomainRecord(Hostname $hostname): void
    {
        if ($this->domainRecord->exists($hostname)) {
            if (!$this->domainRecord->isUpToDate($hostname)) {
                $this->style->writeln(
                    "Domain record '{$hostname}' exists. Updating its ip to ".$this->machineIp->toString()
                );
                $this->domainRecord->update($hostname);
            } else {
                $this->style->writeln("Domain record '{$hostname}' exists and its ip is up-to-date.");
            }
        } else {
            $this->style->writeln(
                "Domain record '{$hostname}' does not exist. Creating it with ip ".$this->machineIp->toString()
            );
            $this->domainRecord->create($hostname);
        }
    }

    private function removeDomainRecord(Hostname $hostname): void
    {
        if ($this->domainRecord->exists($hostname)) {
            $this->style->writeln("Removing domain record '{$hostname}'");
            $this->domainRecord->delete($hostname);
        } else {
            $this->style->writeln("Domain record '{$hostname}' was already removed");
        }
    }

}