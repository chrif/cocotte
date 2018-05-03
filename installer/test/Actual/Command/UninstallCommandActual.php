<?php declare(strict_types=1);

namespace Cocotte\Test\Actual\Command;

use Cocotte\Command\UninstallCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class UninstallCommandActual
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

    public function service(): UninstallCommand
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(UninstallCommand::class);
    }

}
