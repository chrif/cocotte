<?php

declare(strict_types=1);

namespace Chrif\Cocotte\DependencyInjection;

use Chrif\Cocotte\Configuration\ConfigurationValue;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationValuePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $configs = $container->findTaggedServiceIds('configuration');

        foreach ($configs as $id => $attributes) {
            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());

            if (!$r = $container->getReflectionClass($class)) {
                throw new InvalidArgumentException(
                    sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id)
                );
            }
            if ($r->implementsInterface(ConfigurationValue::class)) {
                $definition->setFactory([$class, "fromRoot"]);
            } else {
                $container->removeDefinition($id);
            }
        }
    }
}