<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Style\SymfonyStyle;

final class CocotteStyle extends SymfonyStyle implements Style
{

    public function ok($message)
    {
        $this->block($message, 'OK', null, ' ', true);
    }

    public function help($message)
    {
        $this->block($message, 'HELP', null, ' ', false, false);
    }

    public function pause()
    {
        $this->ask($this->quittableQuestion("Press <options=bold>ENTER</> to continue"));
    }

    public function optionHelp(string $title, array $message): string
    {
        return "<options=bold,underscore>$title</>"."\n".implode("\n", $message)."\n";
    }

    public function quittableQuestion($message): string
    {
        return "$message or press CTRL+D to quit";
    }

    public function link(string $url): string
    {
        return "<info>$url</info> \u{1F517} ";
    }
}