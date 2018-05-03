<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Command;

use Cocotte\Command\UninstallCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class UninstallCommandMother
{
    /**
     * @var TestCase
     */
    private $testCase;

    private function __construct()
    {
    }

    public static function get(TestCase $testCase): self
    {
        $mother = new self();
        $mother->testCase = $testCase;

        return $mother;
    }

    public function service(ContainerInterface $container): UninstallCommand
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $container->get(UninstallCommand::class);
    }
}
