<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Cocotte\Console\CommandConfiguredEvent;
use Cocotte\Console\CommandEventStore;
use Cocotte\Environment\EnvironmentEventStore;
use Cocotte\Environment\EnvironmentLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MachineRequiredListener implements EventSubscriberInterface
{
    /**
     * @var MachineState
     */
    private $machineState;
    /**
     * @var MachineName
     */
    private $machineName;

    public function __construct(MachineState $machineState, MachineName $machineName)
    {
        $this->machineState = $machineState;
        $this->machineName = $machineName;
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_CONFIGURED => 'onCommandConfigured',
            EnvironmentEventStore::ENVIRONMENT_LOADED => 'onEnvironmentLoaded',
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

    public function onEnvironmentLoaded(EnvironmentLoadedEvent $event)
    {
        if ($event->environment() instanceof MachineRequired) {
            if (!$this->machineState->isRunning()) {
                throw new \Exception(
                    "Could not find a running machine named '{$this->machineName}'. ".
                    "Did you create a machine with the install command before ? ".
                    "Did you provide the correct machine name ?"
                );
            }
        }
    }

}