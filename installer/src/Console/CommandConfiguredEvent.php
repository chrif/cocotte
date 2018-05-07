<?php declare(strict_types=1);

namespace Cocotte\Console;

use Symfony\Component\EventDispatcher\Event;

final class CommandConfiguredEvent extends Event
{

    /**
     * @var CommandInterface
     */
    private $command;

    public function __construct(CommandInterface $command)
    {
        $this->command = $command;
    }

    public function command(): CommandInterface
    {
        return $this->command;
    }

}