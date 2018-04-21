<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractCommand extends Command implements CommandInterface
{
    /**
     * @return EventDispatcherInterface
     */
    abstract protected function eventDispatcher(): EventDispatcherInterface;

    final protected function configure()
    {
        $this->eventDispatcher()->dispatch(
            CommandEventStore::COMMAND_CONFIGURE,
            new CommandConfigureEvent($this, $this->getDefinition())
        );
        $this->doConfigure();
    }

    abstract protected function doConfigure(): void;

    final protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->eventDispatcher()->dispatch(
            CommandEventStore::COMMAND_INITIALIZE,
            new CommandInitializeEvent($this, $input)
        );
    }

    final protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->eventDispatcher()->dispatch(
            CommandEventStore::COMMAND_INTERACT,
            new CommandInteractEvent($this, $input)
        );
        $this->doInteract($input, $output);
    }

    protected function doInteract(InputInterface $input, OutputInterface $output): void
    {

    }

    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventDispatcher()->dispatch(
            CommandEventStore::COMMAND_EXECUTE,
            new CommandExecuteEvent($this, $input)
        );

        return $this->doExecute($input, $output);
    }

    abstract protected function doExecute(InputInterface $input, OutputInterface $output);

}