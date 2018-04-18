<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

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

    public function __construct(OptionProviderRegistry $registry, InteractionOperator $operator)
    {
        $this->registry = $registry;
        $this->operator = $operator;
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_CONFIGURE => 'onCommandConfigure',
            CommandEventStore::COMMAND_INTERACT => 'onCommandInteract',
        ];
    }

    public function onCommandConfigure(CommandConfigureEvent $event)
    {
        $command = $event->command();
        foreach ($command->optionProviders() as $className) {
            $event->inputDefinition()->addOption(
                $this->registry->providerByClassName($className)->option()
            );
        }
    }

    public function onCommandInteract(CommandInteractEvent $event)
    {
        $command = $event->command();
        foreach ($command->optionProviders() as $className) {
            $this->operator->interact(
                $event->input(),
                $this->registry->providerByClassName($className)
            );
        }
    }

}
