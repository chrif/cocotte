<?php declare(strict_types=1);

namespace Chrif\Cocotte\DependencyInjection;

use Chrif\Cocotte\Environment\LazyEnvironmentLoader;
use Chrif\Cocotte\Environment\LazyEnvironmentValue;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class EnvironmentValuePass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $configs = $container->findTaggedServiceIds('environment');

        foreach ($configs as $id => $attributes) {
            $valueDefinition = $container->getDefinition($id);
            $valueClass = $container->getParameterBag()->resolveValue($valueDefinition->getClass());
            $lazyLoaderDefinition = $container->getDefinition(LazyEnvironmentLoader::class);

            if (!$r = $container->getReflectionClass($valueClass)) {
                throw new InvalidArgumentException(
                    sprintf('Class "%s" used for service "%s" cannot be found.', $valueClass, $id)
                );
            }
            if ($r->implementsInterface(LazyEnvironmentValue::class)) {
                $valueDefinition->setLazy(true);
                $valueDefinition->setFactory([$valueClass, "fromEnv"]);
                $lazyLoaderDefinition->addMethodCall('addValue', [new Reference($id)]);
            }
        }
    }
}