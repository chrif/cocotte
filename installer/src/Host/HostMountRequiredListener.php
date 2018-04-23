<?php declare(strict_types=1);

namespace Cocotte\Host;

use Cocotte\Console\CommandConfiguredEvent;
use Cocotte\Console\CommandEventStore;
use Cocotte\Console\CommandInitializeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class HostMountRequiredListener implements EventSubscriberInterface
{
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