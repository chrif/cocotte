<?php declare(strict_types=1);

namespace Cocotte\Command;

use Cocotte\Console\AbstractCommand;
use Cocotte\Console\DocumentedCommand;
use Cocotte\Console\InteractionOperator;
use Cocotte\Console\OptionProviderRegistry;
use Cocotte\Console\Style;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Help\DefaultExamples;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikUsername;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WizardCommand extends AbstractCommand implements DocumentedCommand
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

    /**
     * @codeCoverageIgnore
     * @param Style $style
     * @param EventDispatcherInterface $eventDispatcher
     * @param OptionProviderRegistry $optionProviderRegistry
     * @param InteractionOperator $operator
     */
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
            ->setHelp(
                $this->formatHelp($this->description(), $this->example())
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $this->style->help(
            $this->style->optionHelp("Cocotte Wizard", $this->optionHelpMessage())
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

    /**
     * @codeCoverageIgnore
     * @return array
     */
    private function completeMessage(): array
    {
        return [
            "A command will be printed to the terminal.",
            "Run the command from a location on your computer where you usually put new project code.",
            "Afterwards, two directories will be created:\n- one named 'machine' that you must leave there ".
            "and never edit (it is used by Docker Machine to login to your cloud machine),\n- and one named 'traefik' ".
            "that you can edit all you want and which is ready for Git version control: this is your new Traefik project.",
            "Thank you for trying Cocotte!",
        ];
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    private function description(): string
    {
        return $description = /** @lang text */
            "Interactively build a simple '<info>install</info>' command for <options=bold>Cocotte</>.";
    }

    /**
     * @codeCoverageIgnore
     * @param $token
     * @param $traefikHostname
     * @param $traefikPassword
     * @param $traefikUsername
     * @return string
     */
    private function command(
        string $token,
        string $traefikHostname,
        string $traefikPassword,
        string $traefikUsername
    ): string {
        $command = (new DefaultExamples())->install(
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

    /**
     * @return string
     */
    private function example(): string
    {
        return 'docker run -it --rm chrif/cocotte wizard';
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    private function optionHelpMessage(): array
    {
        return [
            "This wizard helps you build a simple '<info>install</info>' command for Cocotte.",
            "It assumes that you own a domain name and can change its name servers.",
            "Cocotte documentation: ".$this->style->link('https://github.com/chrif/cocotte'),
        ];
    }
}