<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Darsyn\IP\IP;
use DigitalOceanV2\Api;
use DigitalOceanV2\Entity;

final class Domain
{

    /**
     * @var Api\Domain
     */
    private $domainApi;

    /**
     * @param Api\Domain $domainApi
     */
    public function __construct(Api\Domain $domainApi)
    {
        $this->domainApi = $domainApi;
    }

    public function create(Hostname $hostname, IP $ip): Entity\Domain
    {
        return $this->domainApi->create(
            $hostname->domainName(),
            $ip->getShortAddress()
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