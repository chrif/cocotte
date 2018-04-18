<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\AbstractCommand;
use Chrif\Cocotte\Console\OptionProviderRegistry;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikPassword;
use Chrif\Cocotte\Template\Traefik\TraefikUsername;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WizardCommand extends AbstractCommand
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

    public function __construct(
        Style $style,
        EventDispatcherInterface $eventDispatcher,
        OptionProviderRegistry $optionProviderRegistry
    ) {
        $this->style = $style;
        $this->eventDispatcher = $eventDispatcher;
        $this->optionProviderRegistry = $optionProviderRegistry;
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
        $this
            ->setName('wizard')
            ->setDescription("Interactively build a simple '<info>install</info>' command for <options=bold>Cocotte</>");
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $this->style->help(
            $this->style->optionHelp(
                "Cocotte Wizard",
                [
                    "This wizard helps you build a simple '<info>install</info>' command for Cocotte.",
                    "It assumes that you own a domain name and can change its name servers.",
                    "Read Cocotte documentation at ".$this->style->link('github.com/chrif/cocotte'),
                ]
            )
        );
        $this->style->pause();

        $token = $this->ask(ApiToken::OPTION_NAME);
        $traefikHostname = $this->ask(TraefikHostname::OPTION_NAME);
        $traefikUsername = $this->ask(TraefikUsername::OPTION_NAME);
        $traefikPassword = $this->ask(TraefikPassword::OPTION_NAME);

        $this->style->block(
            [
                "A command will be printed to the terminal.",
                "Run the command from a location on your computer where you usually put new project code.",
                "Afterwards, two directories will be created:\n- one named 'machine' that you must leave there ".
                "and never edit (it is used by Docker Machine to login to your cloud machine),\n- and one named 'traefik' ".
                "that you can edit all you want and which is ready for Git version control: this your new Traefik project.",
                "Thank you for trying Cocotte!",
            ],
            'COMPLETE',
            'fg=black;bg=green',
            ' ',
            true,
            false
        );
        $this->style->pause();

        $this->style->writeln(
            <<<EOF
<options=bold,underscore>Run this command:</>
docker run -it --rm \
-v "$(pwd)":/host \
-v /var/run/docker.sock:/var/run/docker.sock:ro \
chrif/cocotte install \
--digital-ocean-api-token="$token" \
--traefik-ui-hostname="$traefikHostname" \
--traefik-ui-password="$traefikPassword" \
--traefik-ui-username="$traefikUsername";

EOF
        );
    }

    private function ask(string $name): string
    {
        return $this->optionProviderRegistry->providerByOptionName($name)->ask();
    }
}