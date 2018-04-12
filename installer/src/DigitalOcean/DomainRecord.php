<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Machine\MachineIp;
use Chrif\Cocotte\Template\AppHost;
use DigitalOceanV2\Api;
use DigitalOceanV2\Entity;

final class DomainRecord
{

    const A = 'A';

    /**
     * @var Api\DomainRecord
     */
    private $domainRecordApi;

    /**
     * @var Domain
     */
    private $domain;

    /**
     * @var MachineIp
     */
    private $machineIp;

    /**
     * @param Api\DomainRecord $domainRecordApi
     * @param Domain $domain
     * @param MachineIp $machineIp
     */
    public function __construct(Api\DomainRecord $domainRecordApi, Domain $domain, MachineIp $machineIp)
    {
        $this->domainRecordApi = $domainRecordApi;
        $this->domain = $domain;
        $this->machineIp = $machineIp;
    }

    public function update(AppHost $host): Entity\DomainRecord
    {
        $record = $this->get($host);

        return $this->domainRecordApi->updateData(
            $host->domainName(),
            $record->id,
            $this->machineIp->value()
        );
    }

    public function create(AppHost $host): Entity\DomainRecord
    {
        return $this->domainRecordApi->create(
            $host->domainName(),
            self::A,
            $host->recordName(),
            $this->machineIp->value()
        );
    }

    public function delete(AppHost $host): void
    {
        $record = $this->get($host);

        $this->domainRecordApi->delete(
            $host->domainName(),
            $record->id
        );
    }

    public function get(AppHost $host): Entity\DomainRecord
    {
        $records = $this->domainRecordApi->getAll($host->domainName());

        foreach ($records as $record) {
            if (!$this->isTypeARecord($record)) {
                continue;
            }
            if ($host->matchDomainRecord($record)) {
                return $record;
            }
        }

        throw new \Exception('record does not exist');
    }

    public function exists(AppHost $host): bool
    {
        $records = $this->domainRecordApi->getAll($host->domainName());

        foreach ($records as $record) {
            if (!$this->isTypeARecord($record)) {
                continue;
            }
            if ($host->matchDomainRecord($record)) {
                return true;
            }
        }

        return false;
    }

    public function isUpToDate(AppHost $host): bool
    {
        return $this->get($host)->data === $this->machineIp->value();
    }

    private function isTypeARecord($record): bool
    {
        return $record->type === self::A;
    }

}