<?php declare(strict_types=1);

namespace Cocotte\Console;

use Symfony\Component\Console\Output\OutputInterface;
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

    public function complete($messages): void
    {
        $this->block($messages, 'COMPLETE', 'fg=black;bg=green', ' ', true, false);
    }

    public function pause()
    {
        $this->ask($this->quittableQuestion("Press <options=bold>ENTER</> to continue"));
    }

    public function optionHelp(string $title, array $message): string
    {
        return "<options=bold,underscore>{$title}</>"."\n".implode("\n", $message)."\n";
    }

    public function quittableQuestion($message): string
    {
        return "$message or press CTRL+D to quit";
    }

    public function link(string $url): string
    {
        return "<info>$url</info> \u{1F517} ";
    }

    public function hostnameHelp(string $name, string $subdomain): array
    {
        return [
            "This the fully qualified domain name for your $name.",
            "It has to be with a subdomain like in '<info>$subdomain.mydomain.com</info>', in which case \n".
            "'<info>mydomain.com</info>' must point to the name servers of Digital Ocean, and Cocotte \n".
            "will create and configure the '<info>$subdomain</info>' subdomain for you.",
            "Cocotte validates that the name servers of the domain you enter are Digital \nOcean's. ".
            "How to point to Digital Ocean name servers: ".$this->link('https://goo.gl/SJnw2c')."\n".
            "Please note that when a domain is newly registered, or the name servers are \nchanged, you can expect ".
            "a propagation time up to 24 hours.",
        ];
    }

    public function verbose($messages): void
    {
        $this->writeln($messages, OutputInterface::VERBOSITY_VERBOSE);
    }

    public function veryVerbose($messages): void
    {
        $this->writeln($messages, OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    public function debug($messages): void
    {
        $this->writeln($messages, OutputInterface::VERBOSITY_DEBUG);
    }
}