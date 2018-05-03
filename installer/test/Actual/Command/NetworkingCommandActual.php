<?php declare(strict_types=1);

namespace Cocotte\Test\Actual\Command;

use Cocotte\Command\NetworkingCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class NetworkingCommandActual
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

    public function service(): NetworkingCommand
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(NetworkingCommand::class);
    }
}