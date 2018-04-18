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

    public function verbose(\Closure $closure): void
    {
        $verbosity = $this->verbosities() & self::VERBOSITY_VERBOSE;
        if ($verbosity <= $this->getVerbosity()) {
            $closure();
        }
    }

    public function hostnameHelp(string $name, string $subdomain): array
    {
        return [
            "This the fully qualified domain name for your $name.",
            "It has to be with a subdomain like in '<info>$subdomain.mydomain.com</info>', in which case \n".
            "'<info>mydomain.com</info>' must point to the name servers of Digital Ocean, and Cocotte \n".
            "will create and configure the '<info>$subdomain</info>' subdomain for you.",
            "Cocotte validates that the name servers of the domain you enter are Digital \nOcean's. ".
            "How to point to Digital Ocean name servers: ".$this->link('goo.gl/SJnw2c')."\n".
            "Please note that when a domain is newly registered, or the name servers are \nchanged, you can expect ".
            "a propagation time up to 24 hours.",
        ];
    }

    /**
     * @return int
     */
    private function verbosities(): int
    {
        return self::VERBOSITY_QUIET | self::VERBOSITY_NORMAL | self::VERBOSITY_VERBOSE |
            self::VERBOSITY_VERY_VERBOSE | self::VERBOSITY_DEBUG;
    }

}