<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\DigitalOcean;

use Cocotte\DigitalOcean\DomainRecord;
use Psr\Container\ContainerInterface;

final class DomainRecordActual
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private function __construct()
    {
    }

    public static function get(ContainerInterface $container): self
    {
        $actual = new self();
        $actual->container = $container;

        return $actual;
    }

    public function service(): DomainRecord
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(DomainRecord::class);
    }
}