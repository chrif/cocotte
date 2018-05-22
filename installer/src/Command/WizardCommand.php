<?php declare(strict_types=1);

namespace Cocotte\Command;

use Cocotte\Console\AbstractCommand;
use Cocotte\Console\DocumentedCommand;
use Cocotte\Console\InteractionOperator;
use Cocotte\Console\OptionProviderRegistry;
use Cocotte\Console\Style;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\DnsValidated;
use Cocotte\Help\DefaultExamples;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikUsername;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WizardCommand extends AbstractCommand implements DocumentedCommand, DnsValidated
{
    /**
     * @var Style
     */
    private $style;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var OptionProviderRegistry
     */
    private $optionProviderRegistry;

    /**
     * @var InteractionOperator
     */
    private $operator;

    public function __construct(
        Style $style,
        EventDispatcherInterface $eventDispatcher,
        OptionProviderRegistry $optionProviderRegistry,
        InteractionOperator $operator
    ) {
        $this->style = $style;
        $this->eventDispatcher = $eventDispatcher;
        $this->optionProviderRegistry = $optionProviderRegistry;
        $this->operator = $operator;
        parent::__construct();
    }

    public function optionProviders(): array
    {
        return [];
    }

    protected function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function doConfigure(): void
    {
        $this->setName('wizard')
            ->setDescription($this->description())
            ->setHelp($this->description());
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $this->style->help(
            $this->style->optionHelp("Cocotte Wizard", $this->welcomeMessage())
        );
        $this->style->pause();

        $token = $this->ask(ApiToken::OPTION_NAME);
        $traefikHostname = $this->ask(TraefikHostname::OPTION_NAME);
        $traefikUsername = $this->ask(TraefikUsername::OPTION_NAME);
        $traefikPassword = $this->ask(TraefikPassword::OPTION_NAME);

        $this->style->complete($this->completeMessage());
        $this->style->pause();

        $this->style->writeln(
            $this->command($token, $traefikHostname, $traefikPassword, $traefikUsername)
        );
    }

    private function ask(string $name): string
    {
        return $this->operator->ask($this->optionProviderRegistry->providerByOptionName($name));
    }

    private function completeMessage(): array
    {
        return [
            "A command will be printed to the terminal.",
            "Run the command from a location on your computer where you usually put\n".
            "new project code.",
            "Afterwards, two directories will be created:",
            "1. A 'machine' directory that you must leave there ".
            "and never edit. It is\n".
            "   used by Docker Machine to login to your cloud machine.",
            "2. A 'traefik' directory ".
            "that you can edit all you want and which is\n".
            "   ready for version control. This is your new Traefik project.",
            "Thank you for trying Cocotte!",
        ];
    }

    private function description(): string
    {
        return $description = /** @lang text */
            "Interactively build a simple '<info>install</info>' command for <options=bold>Cocotte</>.";
    }

    private function command(
        string $token,
        string $traefikHostname,
        string $traefikPassword,
        string $traefikUsername
    ): string {
        $command = (new DefaultExamples)->install(
            $token,
            $traefikHostname,
            $traefikPassword,
            $traefikUsername
        );

        return <<<EOF
<options=bold,underscore>Run this command:</>
{$command}

EOF;
    }

    private function welcomeMessage(): array
    {
        return [
            "This wizard helps you build a simple '<info>install</info>' command for Cocotte.",
            "It assumes that you own a domain name and can change its name servers.",
            "Cocotte documentation: ".$this->style->link('https://github.com/chrif/cocotte'),
        ];
    }
}