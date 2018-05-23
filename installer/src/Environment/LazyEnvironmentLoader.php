<?php declare(strict_types=1);

namespace Cocotte\Environment;

use Assert\Assertion;
use Cocotte\Console\CommandEventStore;
use Cocotte\Console\CommandExecuteEvent;
use Cocotte\Console\Style;
use Cocotte\Shell\Env;
use ProxyManager\Proxy\VirtualProxyInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LazyEnvironmentLoader implements EventSubscriberInterface
{
    /**
     * @var LazyEnvironmentValue|VirtualProxyInterface[]
     */
    private $values = [];
    /**
     * @var Style
     */
    private $style;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var Env
     */
    private $env;

    public function __construct(Style $style, EventDispatcherInterface $eventDispatcher, Env $env)
    {
        $this->style = $style;
        $this->eventDispatcher = $eventDispatcher;
        $this->env = $env;
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_EXECUTE => 'onCommandExecute',
        ];
    }

    public function onCommandExecute(CommandExecuteEvent $event)
    {
        $command = $event->command();
        if ($command instanceof LazyEnvironment) {
            $this->load($command, $event->input());
        }
    }

    public function registerValue(LazyEnvironmentValue $value)
    {
        $this->values[] = $value;
    }

    public function load(LazyEnvironment $environment, InputInterface $input)
    {
        foreach ($environment->lazyEnvironmentValues() as $className) {
            $lazyValue = $this->getLazyValue($className);
            if ($lazyValue instanceof LazyExportableOption) {
                $this->exportOption($input, $lazyValue);
            }
            $this->initializeProxy($lazyValue);
        }
        $this->eventDispatcher->dispatch(
            EnvironmentEventStore::ENVIRONMENT_LOADED,
            new EnvironmentLoadedEvent($environment)
        );
        $this->style->debug("Lazy loaded env:\n".print_r(getenv(), true));
    }

    private function getLazyValue(string $className): VirtualProxyInterface
    {
        foreach ($this->values as $value) {
            if ($value instanceof $className) {
                return $value;
            }
        }
        throw new \Exception("Could not find '$className'");
    }

    private function initializeProxy(VirtualProxyInterface $lazyValue)
    {
        if ($lazyValue->isProxyInitialized()) {
            throw new \Exception(
                sprintf(
                    "Managing a lazy environment value ".
                    "'%s' which is already initialized.",
                    get_class($lazyValue->getWrappedValueHolderValue())
                )
            );
        }
        $lazyValue->initializeProxy();
        if ($lazyValue instanceof LazyLoadAware) {
            $lazyValue->onLazyLoad($this->env);
        }
    }

    private function exportOption(InputInterface $input, LazyExportableOption $optionExportValue): void
    {
        $name = $optionExportValue::optionName();
        Assertion::true($input->hasOption($name), sprintf("input does not have an option named '%s'", $name));
        $value = $input->getOption($name);
        if (null !== $value) {
            $optionExportValue::toEnv($value, $this->env);
        }
    }

}