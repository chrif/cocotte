<?php declare(strict_types=1);

namespace Chrif\Cocotte\DependencyInjection;

use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
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
            $managerDefinition = $container->getDefinition(EnvironmentManager::class);

            if (!$r = $container->getReflectionClass($valueClass)) {
                throw new InvalidArgumentException(
                    sprintf('Class "%s" used for service "%s" cannot be found.', $valueClass, $id)
                );
            }
            if ($r->implementsInterface(ImportableValue::class)) {
                $valueDefinition->setFactory([$valueClass, "fromEnv"]);
                $valueDefinition->setLazy(true);
                $managerDefinition->addMethodCall('addImportableValue', [new Reference($id)]);
            }
            if ($r->implementsInterface(ExportableValue::class)) {
                $managerDefinition->addMethodCall('addExportableValue', [new Reference($id)]);
            }
        }
    }
}