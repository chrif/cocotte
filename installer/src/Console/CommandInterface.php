<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

interface CommandInterface
{
    /**
     * @return string[]
     */
    public function optionProviders(): array;

    /**
     * Sets the help for the command.
     *
     * @param string $help The help for the command
     *
     * @return $this
     */
    public function setHelp($help);

    /**
     * Returns the help for the command.
     *
     * @return string The help for the command
     */
    public function getHelp();
}