<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\DigitalOcean;

use Cocotte\DigitalOcean\DnsValidator;
use Psr\Container\ContainerInterface;

final class DnsValidatorActual
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

    public function service(): DnsValidator
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(DnsValidator::class);
    }
}