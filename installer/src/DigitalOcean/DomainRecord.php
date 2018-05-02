<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Cocotte\Machine\MachineIp;
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

    public function update(Hostname $hostname): Entity\DomainRecord
    {
        $record = $this->get($hostname);

        return $this->domainRecordApi->updateData(
            $hostname->domainName(),
            $record->id,
            $this->machineIp->toString()
        );
    }

    public function create(Hostname $hostname): Entity\DomainRecord
    {
        return $this->domainRecordApi->create(
            $hostname->domainName(),
            self::A,
            $hostname->recordName(),
            $this->machineIp->toString()
        );
    }

    public function delete(Hostname $hostname): void
    {
        $record = $this->get($hostname);

        $this->domainRecordApi->delete(
            $hostname->domainName(),
            $record->id
        );
    }

    public function get(Hostname $hostname): Entity\DomainRecord
    {
        $records = $this->domainRecordApi->getAll($hostname->domainName());

        foreach ($records as $record) {
            if (!$this->isTypeARecord($record)) {
                continue;
            }
            if ($hostname->matchDomainRecord($record)) {
                return $record;
            }
        }

        throw new \Exception('record does not exist');
    }

    public function exists(Hostname $hostname): bool
    {
        $records = $this->domainRecordApi->getAll($hostname->domainName());

        foreach ($records as $record) {
            if (!$this->isTypeARecord($record)) {
                continue;
            }
            if ($hostname->matchDomainRecord($record)) {
                return true;
            }
        }

        return false;
    }

    public function isUpToDate(Hostname $hostname): bool
    {
        return $this->get($hostname)->data === $this->machineIp->toString();
    }

    private function isTypeARecord($record): bool
    {
        return $record->type === self::A;
    }

}