<?php declare(strict_types=1);

namespace Cocotte\Test;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TestCompilerPass implements CompilerPassInterface
{
    /**
     * @var bool
     */
    private $useConsoleOutput;

    public function __construct(bool $useConsoleOutput)
    {
        $this->useConsoleOutput = $useConsoleOutput;
    }

    public function process(ContainerBuilder $container)
    {
        $this->configureInput($container);
        $this->configureOutput($container);
    }

    private function configureInput(ContainerBuilder $container): void
    {
        // custom input not pre populated with $_SERVER['argv']
        $inputDefinition = $container->getDefinition(InputInterface::class);
        $inputDefinition->setClass(ArrayInput::class);
        $inputDefinition->setArguments([
            [],
        ]);
    }

    private function configureOutput(ContainerBuilder $container): void
    {
        $outputDefinition = $container->getDefinition(OutputInterface::class);
        if ($this->useConsoleOutput) {
            // we are already using it but output is quiet by default in tests
            $outputDefinition->setArguments([
                OutputInterface::VERBOSITY_QUIET,
            ]);
        } else {
            // use memory output with normal verbosity
            $outputDefinition->setClass(StreamOutput::class);
            $outputDefinition->setArguments([
                fopen('php://memory', 'w', false),
            ]);
        }
    }
}
