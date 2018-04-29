<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Cocotte\Console\CommandConfiguredEvent;
use Cocotte\Console\CommandEventStore;
use Cocotte\Console\CommandInitializeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MachineRequiredListener implements EventSubscriberInterface
{
    /**
     * @var MachineState
     */
    private $machineState;

    public function __construct(MachineState $machineState)
    {
        $this->machineState = $machineState;
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_CONFIGURED => 'onCommandConfigured',
            CommandEventStore::COMMAND_INITIALIZE => 'onCommandInitialize',
        ];
    }

    public function onCommandConfigured(CommandConfiguredEvent $event)
    {
        $command = $event->command();

        if ($command instanceof MachineRequired) {
            $command->setHelp(
                $command->getHelp().
                '
<info>This command requires the Docker Machine created by the `install` command.</info>
');
        }
    }

    public function onCommandInitialize(CommandInitializeEvent $event)
    {
        if ($event->command() instanceof MachineRequired) {
            if (!$this->machineState->exists()) {
                throw new \Exception("Could not find a machine. ".
                    "Did you create a machine with the install command before? ".
                    "Did you provide the correct machine name?");
            }
        }
    }

}