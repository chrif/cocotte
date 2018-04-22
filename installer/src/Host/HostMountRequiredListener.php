<?php declare(strict_types=1);

namespace Chrif\Cocotte\Host;

use Chrif\Cocotte\Console\CommandConfigureEvent;
use Chrif\Cocotte\Console\CommandEventStore;
use Chrif\Cocotte\Console\CommandInitializeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class HostMountRequiredListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_CONFIGURE => 'onCommandConfigure',
            CommandEventStore::COMMAND_INITIALIZE => 'onCommandInitialize',
        ];
    }

    public function onCommandConfigure(CommandConfigureEvent $event)
    {
        $command = $event->command();

        if ($command instanceof HostMountRequired) {
            $command->setHelp(
                $command->getHelp().
                '
<info>This command requires 2 volumes:</info>
  * "$(pwd)":/host
  * /var/run/docker.sock:/var/run/docker.sock:ro
');
        }
    }

    public function onCommandInitialize(CommandInitializeEvent $event)
    {
        if ($event->command() instanceof HostMountRequired) {
            try {
                HostMount::fromEnv();
            } catch (HostException $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

}