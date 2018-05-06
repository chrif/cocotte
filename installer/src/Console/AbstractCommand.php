<?php declare(strict_types=1);

namespace Cocotte\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractCommand extends Command implements CommandInterface
{
    public function getSynopsis($short = false)
    {
        return "docker run -it --rm chrif/cocotte ".parent::getSynopsis($short);
    }

    protected function formatHelp(string $description, string $example): string
    {
        return $description."\n\n<info>Example:</info>\n```\n$ {$example}\n```";
    }

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
        $this->eventDispatcher()->dispatch(
            CommandEventStore::COMMAND_CONFIGURED,
            new CommandConfiguredEvent($this)
        );
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
