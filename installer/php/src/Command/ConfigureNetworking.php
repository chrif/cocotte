<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Configuration\App\AppHost;
use Chrif\Cocotte\Configuration\App\AppHostCollection;
use Chrif\Cocotte\Configuration\Droplet\DropletIp;
use Chrif\Cocotte\DigitalOcean\Domain;
use Chrif\Cocotte\DigitalOcean\DomainRecord;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigureNetworking extends Command
{
    /**
     * @var AppHostCollection|AppHost[]
     */
    private $hosts;

    /**
     * @var DomainRecord
     */
    private $domainRecord;

    /**
     * @var Domain
     */
    private $domain;

    /**
     * @var DropletIp
     */
    private $dropletIp;

    public function __construct(
        DomainRecord $domainRecord,
        Domain $domain,
        DropletIp $dropletIp
    ) {
        $this->domainRecord = $domainRecord;
        $this->domain = $domain;
        $this->dropletIp = $dropletIp;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cocotte:configure-networking')
            ->setDescription('Configure networking of Digital Ocean')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Comma-separated list of hosts')
            ->addOption('remove', null, InputOption::VALUE_NONE, 'Remove networking for hosts')
            ->setAliases(array('cn'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->hosts = AppHostCollection::fromString($input->getArgument('hosts'));

        foreach ($this->hosts as $host) {
            $output->writeln('Configuring '.$host->value());
            if ($input->getOption('remove')) {
                $this->removeDomainRecord($output, $host);
            } else {
                $this->configureDomain($output, $host);
            }
        }
    }

    private function configureDomain(OutputInterface $output, AppHost $host): void
    {
        if (!$this->domain->exists($host)) {
            $output->writeln(
                "Domain '{$host->domain()}' did not exist. Creating it and adding ".
                "{$host->toRoot()->value()} with ip ".$this->dropletIp->value()
            );
            $this->domain->create($host);
        } else {
            if (!$host->isRoot()) {
                $this->configureDomainRecord($output, $host->toRoot());
            }
        }

        $this->configureDomainRecord($output, $host);
    }

    private function configureDomainRecord(OutputInterface $output, AppHost $host): void
    {
        if ($this->domainRecord->exists($host)) {
            if (!$this->domainRecord->isUpToDate($host)) {
                $output->writeln(
                    "Domain record '{$host->value()}' exists. Updating its ip to ".$this->dropletIp->value()
                );
                $this->domainRecord->update($host);
            } else {
                $output->writeln("Domain record '{$host->value()}' exists and its ip is up-to-date.");
            }
        } else {
            $output->writeln(
                "Domain record '{$host->value()}' did not exist. Creating it with ip ".$this->dropletIp->value()
            );
            $this->domainRecord->create($host);
        }
    }

    private function removeDomainRecord(OutputInterface $output, AppHost $host): void
    {
        if ($this->domainRecord->exists($host)) {
            $output->writeln("Removing domain record '{$host->value()}'");
            $this->domainRecord->delete($host);
        } else {
            $output->writeln("Domain record '{$host->value()}' was already removed");
        }
    }

}