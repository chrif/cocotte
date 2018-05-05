<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class InputActual
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

    public function service(): InputInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(InputInterface::class);
    }

    public function setInputs(array $inputs): void
    {
        $input = $this->service();
        if ($input instanceof StreamableInputInterface) {
            $stream = fopen('php://memory', 'r+', false);
            fwrite($stream, implode(PHP_EOL, $inputs));
            rewind($stream);
            $input->setStream($stream);
        } else {
            throw new \Exception('Actual input is not Streamable');
        }
    }
}