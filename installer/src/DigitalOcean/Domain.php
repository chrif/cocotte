<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Machine\MachineIp;
use DigitalOceanV2\Api;
use DigitalOceanV2\Entity;

final class Domain
{

    /**
     * @var Api\Domain
     */
    private $domainApi;

    /**
     * @var MachineIp
     */
    private $machineIp;

    /**
     * @param Api\Domain $domainApi
     * @param MachineIp $machineIp
     */
    public function __construct(Api\Domain $domainApi, MachineIp $machineIp)
    {
        $this->domainApi = $domainApi;
        $this->machineIp = $machineIp;
    }

    public function create(Hostname $hostname): Entity\Domain
    {
        return $this->domainApi->create(
            $hostname->domainName(),
            $this->machineIp->value()
        );
    }

    public function delete(Hostname $hostname): void
    {
        $this->domainApi->delete(
            $hostname->domainName()
        );
    }

    public function exists(Hostname $hostname): bool
    {
        $domains = $this->domainApi->getAll();

        foreach ($domains as $domain) {
            if ($domain->name === $hostname->domainName()) {
                return true;
            }
        }

        return false;
    }
}