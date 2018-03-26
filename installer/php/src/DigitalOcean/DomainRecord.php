<?php

declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Configuration\App\AppHost;
use Chrif\Cocotte\Configuration\Droplet\DropletIp;
use DigitalOceanV2\Api;
use DigitalOceanV2\Entity;

class DomainRecord
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
     * @var DropletIp
     */
    private $dropletIp;

    /**
     * @param Api\DomainRecord $domainRecordApi
     * @param Domain $domain
     * @param DropletIp $dropletIp
     */
    public function __construct(Api\DomainRecord $domainRecordApi, Domain $domain, DropletIp $dropletIp)
    {
        $this->domainRecordApi = $domainRecordApi;
        $this->domain = $domain;
        $this->dropletIp = $dropletIp;
    }

    public function update(AppHost $host): Entity\DomainRecord
    {
        $record = $this->get($host);

        return $this->domainRecordApi->updateData(
            $host->domain(),
            $record->id,
            $this->dropletIp->value()
        );
    }

    public function create(AppHost $host): Entity\DomainRecord
    {
        return $this->domainRecordApi->create(
            $host->domain(),
            self::A,
            $host->subDomain(),
            $this->dropletIp->value()
        );
    }

    public function delete(AppHost $host): void
    {
        $record = $this->get($host);

        $this->domainRecordApi->delete(
            $host->domain(),
            $record->id
        );
    }

    public function get(AppHost $host): Entity\DomainRecord
    {
        $records = $this->domainRecordApi->getAll($host->domain());

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
        $records = $this->domainRecordApi->getAll($host->domain());

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
        return $this->get($host)->data === $this->dropletIp->value();
    }

    private function isTypeARecord($record): bool
    {
        return $record->type === self::A;
    }

}