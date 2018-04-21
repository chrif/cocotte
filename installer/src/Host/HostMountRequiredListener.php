<?php declare(strict_types=1);

namespace Chrif\Cocotte\Host;

use Chrif\Cocotte\Console\CommandEventStore;
use Chrif\Cocotte\Console\CommandInitializeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class HostMountRequiredListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_INITIALIZE => 'onCommandInitialize',
        ];
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