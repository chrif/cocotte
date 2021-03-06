<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Command;

use Cocotte\Command\WizardCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class WizardCommandActual
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

    public function service(): WizardCommand
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(WizardCommand::class);
    }
}