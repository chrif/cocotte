<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Help;

use Cocotte\Help\FromEnvExamples;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class FromEnvExamplesActual
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

    public function service(): FromEnvExamples
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(FromEnvExamples::class);
    }

}