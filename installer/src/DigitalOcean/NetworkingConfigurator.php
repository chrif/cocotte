<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Machine\MachineIp;
use Symfony\Component\Console\Output\OutputInterface;

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
        $this->style->writeln('Configuring networking for all the hostnames supplied: '.$hostnames->toString(),
            OutputInterface::VERBOSITY_VERBOSE);
        foreach ($hostnames as $host) {
            $this->style->writeln('Configuring '.$host,
                OutputInterface::VERBOSITY_VERBOSE);
            if ($remove) {
                $this->removeDomainRecord($host);
            } else {
                $this->configureDomain($host);
            }
        }
    }

    private function configureDomain(Hostname $hostname): void
    {
        if (!$this->domain->exists($hostname)) {
            $this->style->writeln(
                "Domain '{$hostname->toRoot()}' does not exist. Creating it and adding ".
                "{$hostname->toRoot()} with ip ".$this->machineIp->toString(),
                OutputInterface::VERBOSITY_VERBOSE
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
                    "Domain record '{$hostname}' exists. Updating its ip to ".$this->machineIp->toString(),
                    OutputInterface::VERBOSITY_VERBOSE
                );
                $this->domainRecord->update($hostname);
            } else {
                $this->style->writeln("Domain record '{$hostname}' exists and its ip is up-to-date.",
                    OutputInterface::VERBOSITY_VERBOSE);
            }
        } else {
            $this->style->writeln(
                "Domain record '{$hostname}' does not exist. Creating it with ip ".$this->machineIp->toString(),
                OutputInterface::VERBOSITY_VERBOSE
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
            $this->style->note("Domain record '{$hostname}' was already removed");
        }
    }

}