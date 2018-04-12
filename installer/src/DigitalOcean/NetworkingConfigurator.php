<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Machine\MachineIp;
use Chrif\Cocotte\Template\AppHost;
use Chrif\Cocotte\Template\AppHostCollection;

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
     * @param AppHostCollection|AppHost[] $appHostCollection
     * @param bool $remove
     */
    public function configure(AppHostCollection $appHostCollection, $remove = false)
    {
        $this->style->title('Configuring networking for all the hosts supplied: ');
        $this->style->listing($appHostCollection->toArray());
        foreach ($appHostCollection as $host) {
            $this->style->section($host);
            if ($remove) {
                $this->removeDomainRecord($host);
            } else {
                $this->configureDomain($host);
            }
        }
        $this->style->success("");
    }

    private function configureDomain(AppHost $host): void
    {
        if (!$this->domain->exists($host)) {
            $this->style->writeln(
                "Domain '{$host->toRoot()}' does not exist. Creating it and adding ".
                "{$host->toRoot()} with ip ".$this->machineIp->value()
            );
            $this->domain->create($host);
        }

        $this->configureDomainRecord($host);
    }

    private function configureDomainRecord(AppHost $host): void
    {
        if ($this->domainRecord->exists($host)) {
            if (!$this->domainRecord->isUpToDate($host)) {
                $this->style->writeln(
                    "Domain record '{$host}' exists. Updating its ip to ".$this->machineIp->value()
                );
                $this->domainRecord->update($host);
            } else {
                $this->style->writeln("Domain record '{$host}' exists and its ip is up-to-date.");
            }
        } else {
            $this->style->writeln(
                "Domain record '{$host}' does not exist. Creating it with ip ".$this->machineIp->value()
            );
            $this->domainRecord->create($host);
        }
    }

    private function removeDomainRecord(AppHost $host): void
    {
        if ($this->domainRecord->exists($host)) {
            $this->style->writeln("Removing domain record '{$host}'");
            $this->domainRecord->delete($host);
        } else {
            $this->style->writeln("Domain record '{$host}' was already removed");
        }
    }

}