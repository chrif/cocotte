<?php

namespace Chrif\Cocotte\DependencyInjection;

use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConsoleCommandPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $ids = array_keys($container->findTaggedServiceIds('console.command', true));

        $refs = array_map(
            function ($id) {
                return new Reference($id);
            },
            $ids
        );

        $console = $container->getDefinition(Application::class);
        $console->addMethodCall('addCommands', [$refs]);
    }
}
