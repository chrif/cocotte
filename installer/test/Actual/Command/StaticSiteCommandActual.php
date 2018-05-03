<?php declare(strict_types=1);

namespace Cocotte\Test\Actual\Command;

use Cocotte\Command\StaticSiteCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class StaticSiteCommandActual
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

    public function service(): StaticSiteCommand
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(StaticSiteCommand::class);
    }

}