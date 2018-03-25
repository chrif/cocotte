<?php

declare(strict_types=1);

namespace Chrif\Cocotte\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(string $serviceResource, string $resource)
    {
        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator());
        $loader->load($serviceResource);

        $container->setParameter('cocotte.resource', $resource);

        $container->addCompilerPass(new ConfigurationValuePass());
        $container->addCompilerPass(new ConsoleCommandPass());

        $container->compile();

        $this->container = $container;
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }

    public function console(): Console
    {
        return $this->container->get(Console::class);
    }
}