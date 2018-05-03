<?php declare(strict_types=1);

namespace Cocotte\Test\Double\DigitalOcean;

use Cocotte\DigitalOcean\Domain;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class DomainMother
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

    public function service(ContainerInterface $container): Domain
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $container->get(Domain::class);
    }

}