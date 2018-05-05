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
        // custom input not pre populated with $_SERVER['argv']
        $definition = $container->getDefinition(InputInterface::class);
        $definition->setClass(ArrayInput::class);
        $definition->setArguments([
            [],
        ]);

        // customize output
        $definition = $container->getDefinition(OutputInterface::class);
        if ($this->useConsoleOutput) {
            // we are already using it but output is quiet by default in tests
            $definition->setArguments([
                OutputInterface::VERBOSITY_QUIET,
            ]);
        } else {
            // use memory output with normal verbosity
            $definition->setClass(StreamOutput::class);
            $definition->setArguments([
                fopen('php://memory', 'w', false),
            ]);
        }
    }
}
