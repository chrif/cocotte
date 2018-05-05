<?php declare(strict_types=1);

namespace Cocotte\DependencyInjection;

use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as Console;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class Application
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param string $serviceResource
     * @param CompilerPassInterface[] $extraPasses
     * @throws \Exception
     */
    public function __construct(string $serviceResource, array $extraPasses = [])
    {
        $container = new ContainerBuilder();
        $container->setProxyInstantiator(new RuntimeInstantiator());

        $loader = new YamlFileLoader($container, new FileLocator());
        $loader->load($serviceResource);

        $container->addCompilerPass(new LazyEnvironmentPass());
        $container->addCompilerPass(new ConsoleCommandPass());
        $container->addCompilerPass(new OptionProviderPass());
        $container->addCompilerPass(new RegisterListenersPass(
            EventDispatcherInterface::class,
            'event.listener',
            'event.subscriber'
        ));

        foreach ($extraPasses as $pass) {
            $container->addCompilerPass($pass);
        }

        $container->compile(true);

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