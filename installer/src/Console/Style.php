<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

interface Style extends OutputInterface, StyleInterface
{
    /**
     * Formats a message as a block of text.
     *
     * @param string|array $messages The message to write in the block
     * @param string|null $type The block type (added in [] on first line)
     * @param string|null $style The style to apply to the whole block
     * @param string $prefix The prefix for the block
     * @param bool $padding Whether to add vertical padding
     * @param bool $escape Whether to escape the message
     */
    public function block($messages, $type = null, $style = null, $prefix = ' ', $padding = false, $escape = true);
}