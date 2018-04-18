<?php declare(strict_types=1);

namespace Chrif\Cocotte\DependencyInjection;

use Assert\Assertion;
use Chrif\Cocotte\Environment\LazyEnvironmentLoader;
use Chrif\Cocotte\Environment\LazyEnvironmentValue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class LazyEnvironmentPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $configs = $container->findTaggedServiceIds(LazyEnvironmentValue::LAZY_ENVIRONMENT);
        $lazyLoaderDefinition = $container->getDefinition(LazyEnvironmentLoader::class);

        foreach ($configs as $id => $attributes) {
            $valueDefinition = $container->getDefinition($id);
            $valueClass = $container->getParameterBag()->resolveValue($valueDefinition->getClass());
            Assertion::true(
                $container->getReflectionClass($valueClass)->implementsInterface(LazyEnvironmentValue::class)
            );
            $valueDefinition->setLazy(true);
            $valueDefinition->setFactory([$valueClass, "fromEnv"]);
            $lazyLoaderDefinition->addMethodCall('registerValue', [new Reference($id)]);
        }
    }
}