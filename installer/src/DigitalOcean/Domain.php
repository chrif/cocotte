<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Configuration\AppHost;
use Chrif\Cocotte\Configuration\MachineIp;
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

    public function create(AppHost $host): Entity\Domain
    {
        return $this->domainApi->create(
            $host->domainName(),
            $this->machineIp->value()
        );
    }

    public function delete(AppHost $host): void
    {
        $this->domainApi->delete(
            $host->domainName()
        );
    }

    public function exists(AppHost $host): bool
    {
        $domains = $this->domainApi->getAll();

        foreach ($domains as $domain) {
            if ($domain->name === $host->domainName()) {
                return true;
            }
        }

        return false;
    }
}