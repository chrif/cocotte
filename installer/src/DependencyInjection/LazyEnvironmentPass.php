<?php declare(strict_types=1);

namespace Cocotte\DependencyInjection;

use Cocotte\Environment\FromEnvLazyFactory;
use Cocotte\Environment\LazyEnvironmentLoader;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Shell\Env;
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
            $reflectionValueClass = $container->getReflectionClass($valueClass);

            if (!$reflectionValueClass->implementsInterface(LazyEnvironmentValue::class)) {
                throw new \Exception(
                    "$valueClass does not implement ".LazyEnvironmentValue::class
                );
            }

            if (!$valueDefinition->getFactory()) {
                if (!$reflectionValueClass->implementsInterface(FromEnvLazyFactory::class)) {
                    throw new \Exception(
                        "There is not custom factory and $valueClass does not implement ".FromEnvLazyFactory::class
                    );
                }
                $valueDefinition->setFactory([$valueClass, "fromEnv"]);
                $valueDefinition->setArguments([new Reference(Env::class)]);
            }

            $valueDefinition->setLazy(true);
            $lazyLoaderDefinition->addMethodCall('registerValue', [new Reference($id)]);
        }
    }
}