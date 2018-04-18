<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\Event;

final class CommandExecuteEvent extends Event
{

    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @var InputInterface
     */
    private $input;

    public function __construct(CommandInterface $command, InputInterface $input)
    {
        $this->command = $command;
        $this->input = $input;
    }

    public function command(): CommandInterface
    {
        return $this->command;
    }

    public function input(): InputInterface
    {
        return $this->input;
    }

}