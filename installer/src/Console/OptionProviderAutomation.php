<?php declare(strict_types=1);

namespace Cocotte\Console;

use Cocotte\Environment\EnvironmentState;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OptionProviderAutomation implements EventSubscriberInterface
{
    /**
     * @var OptionProviderRegistry
     */
    private $registry;

    /**
     * @var InteractionOperator
     */
    private $operator;
    /**
     * @var EnvironmentState
     */
    private $environmentState;

    public function __construct(
        OptionProviderRegistry $registry,
        InteractionOperator $operator,
        EnvironmentState $environmentState
    ) {
        $this->registry = $registry;
        $this->operator = $operator;
        $this->environmentState = $environmentState;
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_CONFIGURE => 'onCommandConfigure',
            CommandEventStore::COMMAND_INITIALIZE => 'onCommandInitialize',
            CommandEventStore::COMMAND_INTERACT => 'onCommandInteract',
        ];
    }

    public function onCommandConfigure(CommandConfigureEvent $event)
    {
        $command = $event->command();

        foreach ($command->optionProviders() as $className) {
            $optionProvider = $this->registry->providerByClassName($className);

            $event->inputDefinition()->addOption($optionProvider->option($this->environmentState));
        }
    }

    public function onCommandInitialize(CommandInitializeEvent $event)
    {
        $command = $event->command();
        $input = $event->input();

        foreach ($command->optionProviders() as $className) {
            $optionProvider = $this->registry->providerByClassName($className);

            $value = $input->getOption($optionProvider->optionName());
            if (is_string($value)) {
                $optionProvider->validate($value);
            }
        }
    }

    public function onCommandInteract(CommandInteractEvent $event)
    {
        $command = $event->command();

        foreach ($command->optionProviders() as $className) {
            $optionProvider = $this->registry->providerByClassName($className);

            $this->operator->interact($event->input(), $optionProvider);
        }
    }
}
