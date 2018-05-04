<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Command;

use Cocotte\Command\BuildDocCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class BuildDocCommandActual
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

    public function service(): BuildDocCommand
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(BuildDocCommand::class);
    }
}