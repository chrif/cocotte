<?php

namespace Cocotte\Test\Collaborator\Console;

use Cocotte\Console\DocumentedCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class SingleArgumentCommandStub extends Command implements DocumentedCommand
{
    /**
     * @var InputArgument
     */
    private $argument;

    public function __construct(InputArgument $argument)
    {
        $this->argument = $argument;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('single-argument')
            ->setDescription('This command has only one argument')
            ->getDefinition()
            ->addArgument($this->argument);
    }
}