<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

use Assert\Assertion;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class OutputActual
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

    public function service(): OutputInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(OutputInterface::class);
    }

    public function getDisplay(): string
    {
        $output = $this->service();
        if ($output instanceof StreamOutput) {
            $stream = $output->getStream();
        } else {
            throw new \Exception('output is not a StreamOutput');
        }

        rewind($stream);

        $contents = stream_get_contents($stream);
        Assertion::string($contents);

        return $contents;
    }
}