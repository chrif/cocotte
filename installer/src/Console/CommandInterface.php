<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

interface CommandInterface
{
    /**
     * @return string[]
     */
    public function optionProviders(): array;

}