<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\DigitalOcean;

use Cocotte\DigitalOcean\Domain;
use Cocotte\DigitalOcean\DomainRecord;
use Cocotte\DigitalOcean\NetworkingConfigurator;
use Cocotte\Machine\MachineIp;
use Cocotte\Test\Collaborator\Console\StyleDouble;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class NetworkingConfiguratorWithFakeMachineIpFixture
{
    /**
     * @var MachineIp
     */
    private $machineIp;
    /**
     * @var NetworkingConfigurator
     */
    private $configurator;
    /**
     * @var DomainRecord
     */
    private $domainRecordApi;
    /**
     * @var Domain
     */
    private $domainApi;

    public function __construct(TestCase $testCase, ContainerInterface $container, MachineIp $machineIp)
    {
        $this->configurator = new NetworkingConfigurator(
            $this->domainRecordApi = new DomainRecord(
                $container->get(\DigitalOceanV2\Api\DomainRecord::class),
                $this->domainApi = new Domain(
                    $container->get(\DigitalOceanV2\Api\Domain::class),
                    $this->machineIp = $machineIp
                ),
                $this->machineIp
            ),
            $this->domainApi,
            $this->machineIp,
            StyleDouble::create($testCase)->mock()
        );
    }

    /**
     * @return MachineIp
     */
    public function machineIp(): MachineIp
    {
        return $this->machineIp;
    }

    /**
     * @return NetworkingConfigurator
     */
    public function configurator(): NetworkingConfigurator
    {
        return $this->configurator;
    }

    /**
     * @return DomainRecord
     */
    public function domainRecordApi(): DomainRecord
    {
        return $this->domainRecordApi;
    }

    /**
     * @return Domain
     */
    public function domainApi(): Domain
    {
        return $this->domainApi;
    }

}