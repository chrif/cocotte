<?php declare(strict_types=1);

namespace Cocotte\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\Event;

final class CommandBeforeInitializeEvent extends Event
{

    /**
     * @var InputInterface
     */
    private $input;

    public function __construct(InputInterface $input)
    {
        $this->input = $input;
    }

    public function input(): InputInterface
    {
        return $this->input;
    }

}