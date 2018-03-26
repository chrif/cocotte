<?php

declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Configuration\App\AppHost;
use Chrif\Cocotte\Configuration\Droplet\DropletIp;
use DigitalOceanV2\Api;
use DigitalOceanV2\Entity;

class Domain
{

    /**
     * @var Api\Domain
     */
    private $domainApi;

    /**
     * @var DropletIp
     */
    private $dropletIp;

    /**
     * @param Api\Domain $domainApi
     * @param DropletIp $dropletIp
     */
    public function __construct(Api\Domain $domainApi, DropletIp $dropletIp)
    {
        $this->domainApi = $domainApi;
        $this->dropletIp = $dropletIp;
    }

    public function create(AppHost $host): Entity\Domain
    {
        return $this->domainApi->create(
            $host->domain(),
            $this->dropletIp->value()
        );
    }

    public function delete(AppHost $host): void
    {
        $this->domainApi->delete(
            $host->domain()
        );
    }

    public function exists(AppHost $host): bool
    {
        $domains = $this->domainApi->getAll();

        foreach ($domains as $domain) {
            if ($domain->name === $host->domain()) {
                return true;
            }
        }

        return false;
    }
}