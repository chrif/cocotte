<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiTokenInteraction;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameInteraction;
use Chrif\Cocotte\Template\Traefik\TraefikPasswordInteraction;
use Chrif\Cocotte\Template\Traefik\TraefikUsernameInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class WizardCommand extends Command
{
    /**
     * @var Style
     */
    private $style;

    /**
     * @var TraefikUsernameInteraction
     */
    private $traefikUsernameInteraction;

    /**
     * @var TraefikPasswordInteraction
     */
    private $traefikPasswordInteraction;

    /**
     * @var TraefikHostnameInteraction
     */
    private $traefikHostnameInteraction;

    /**
     * @var ApiTokenInteraction
     */
    private $apiTokenInteraction;

    public function __construct(
        Style $style,
        TraefikUsernameInteraction $traefikUsernameInteraction,
        TraefikPasswordInteraction $traefikPasswordInteraction,
        TraefikHostnameInteraction $traefikHostnameInteraction,
        ApiTokenInteraction $apiTokenInteraction
    ) {
        $this->style = $style;
        $this->traefikUsernameInteraction = $traefikUsernameInteraction;
        $this->traefikPasswordInteraction = $traefikPasswordInteraction;
        $this->traefikHostnameInteraction = $traefikHostnameInteraction;
        $this->apiTokenInteraction = $apiTokenInteraction;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('wizard')
            ->setDescription('Interactively build a simple install command for Cocotte');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $this->style->help(
            $this->style->optionHelp(
                "Cocotte Wizard",
                [
                    "This wizard helps you get started by building a simple '<info>install</info>' command for Cocotte.",
                    "It assumes that you own a domain name and can change its name servers.",
                    "Read Cocotte documentation at ".$this->style->link('github.com/chrif/cocotte'),
                ]
            )
        );
        $this->style->pause();

        $token = $this->apiTokenInteraction->ask();
        $traefikHostname = $this->traefikHostnameInteraction->ask();
        $traefikUsername = $this->traefikUsernameInteraction->ask();
        $traefikPassword = $this->traefikPasswordInteraction->ask();

        $this->style->block(
            [
                "A command will be printed to the terminal.",
                "Run the command from a location on your computer where you usually put new project code.",
                "Afterwards, two directories will be created: one named 'machine' that you must leave there ".
                "and never edit (it is used by Docker Machine to login to your cloud machine), and one named 'traefik' ".
                "that you can edit all you want and which is ready for Git version control: this your Traefik project.",
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

}