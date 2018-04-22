<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\EventDispatcher\Event;

final class CommandConfiguredEvent extends Event
{

    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @var InputDefinition
     */
    private $inputDefinition;

    public function __construct(CommandInterface $command, InputDefinition $inputDefinition)
    {
        $this->command = $command;
        $this->inputDefinition = $inputDefinition;
    }

    public function command(): CommandInterface
    {
        return $this->command;
    }

    /**
     * @return InputDefinition
     */
    public function inputDefinition(): InputDefinition
    {
        return $this->inputDefinition;
    }

}