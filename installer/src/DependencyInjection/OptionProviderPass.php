<?php declare(strict_types=1);

namespace Cocotte\DependencyInjection;

use Cocotte\Console\OptionProvider;
use Cocotte\Console\OptionProviderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OptionProviderPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $configs = $container->findTaggedServiceIds(OptionProvider::OPTION_PROVIDER);
        $definition = $container->getDefinition(OptionProviderRegistry::class);

        foreach ($configs as $id => $attributes) {
            $definition->addMethodCall('registerProvider', [new Reference($id)]);
        }
    }
}