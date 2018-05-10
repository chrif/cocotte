<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Shell;

use Cocotte\Shell\ProcessRunner;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ProcessRunnerActual
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

    public function service(): ProcessRunner
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(ProcessRunner::class);
    }

}