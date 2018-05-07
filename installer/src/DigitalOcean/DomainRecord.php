<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Darsyn\IP\IP;
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
     * @param Api\DomainRecord $domainRecordApi
     * @param Domain $domain
     */
    public function __construct(Api\DomainRecord $domainRecordApi, Domain $domain)
    {
        $this->domainRecordApi = $domainRecordApi;
        $this->domain = $domain;
    }

    public function update(Hostname $hostname, IP $ip): Entity\DomainRecord
    {
        $record = $this->get($hostname);

        return $this->domainRecordApi->updateData(
            $hostname->domainName(),
            $record->id,
            $ip->getShortAddress()
        );
    }

    public function create(Hostname $hostname, IP $ip): Entity\DomainRecord
    {
        return $this->domainRecordApi->create(
            $hostname->domainName(),
            self::A,
            $hostname->recordName(),
            $ip->getShortAddress()
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

    public function isUpToDate(Hostname $hostname, IP $ip): bool
    {
        return $this->get($hostname)->data === $ip->getShortAddress();
    }

    private function isTypeARecord($record): bool
    {
        return $record->type === self::A;
    }

}