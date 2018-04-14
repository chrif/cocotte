<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\Hostname;
use Chrif\Cocotte\DigitalOcean\DnsValidator;
use Chrif\Cocotte\Filesystem\Filesystem;
use Chrif\Cocotte\Template\Traefik\TraefikUiPassword;
use Chrif\Cocotte\Template\Traefik\TraefikUiUsername;
use DigitalOceanV2\Adapter\GuzzleHttpAdapter;
use DigitalOceanV2\DigitalOceanV2;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class WizardCommand extends Command
{
    /**
     * @var Style
     */
    private $style;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DnsValidator
     */
    private $dnsValidator;

    public function __construct(Style $style, Filesystem $filesystem, DnsValidator $dnsValidator)
    {
        $this->style = $style;
        $this->filesystem = $filesystem;
        $this->dnsValidator = $dnsValidator;
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
        $this->style->title("Welcome to Cocotte Wizard");
        $this->help(
            [
                "This wizard helps you get started by interactively building a simple install command for Cocotte.",
                "It assumes that you own a domain name and can change its name servers.",
                "Visit https://github.com/chrif/cocotte for Cocotte documentation.",
                "Press CTRL+D at any moment to quit.",
            ]
        );
        $this->style->ask("Press Enter to continue");

        $token = $this->getToken();
        $projectPath = $this->getProjectPath();
        $traefikUiHostname = $this->getTraefikUiHostname();
        $traefikUiUsername = $this->getTraefikUiUsername();
        $traefikUiPassword = $this->getTraefikUiPassword();

        $this->style->block(
            [
                "A command will be printed to the terminal. The command first creates the directory '$projectPath' ".
                "if it does not exist and then starts Cocotte from this location.",
                "Afterwards, two directories will be created: one named 'machine' that you must leave there ".
                "and never edit (it is used by Docker Machine to login to your cloud machine), and one named 'traefik' ".
                "that you can edit all you want and which is ready for Git version control.",
                "Thank you for trying Cocotte!",
            ],
            'COMPLETE',
            'fg=black;bg=green',
            ' ',
            true
        );
        $this->style->ask("Press Enter to continue");

        $projectPath = str_replace(" ", "\ ", $projectPath);
        $this->style->writeln(
            <<<EOF
Run this command to let Cocotte create a cloud machine for you:
---------------------------------------------------------------
mkdir -p $projectPath && cd $projectPath && docker run -it --rm \
-v "$(pwd)":/host \
-v /var/run/docker.sock:/var/run/docker.sock:ro \
chrif/cocotte install \
--digital-ocean-api-token="$token" \
--machine-storage-path="$(pwd)/machine" \
--traefik-ui-hostname="$traefikUiHostname" \
--traefik-ui-password="$traefikUiPassword" \
--traefik-ui-username="$traefikUiUsername";
---------------------------------------------------------------
EOF
        );
    }

    private function getTraefikUiUsername(): string
    {
        $this->style->section("Traefik UI username");

        $this->help(
            [
                TraefikUiUsername::HELP,
            ]
        );

        return $this->style->askQuestion(
            (new Question(
                "Choose a username for your Traefik UI",
                "admin"
            ))
                ->setNormalizer(
                    function ($answer): string {
                        return trim((string)$answer);
                    }
                )
                ->setValidator(
                    function (string $answer): string {
                        if (!$answer) {
                            throw new \Exception('No answer given. Try again.');
                        }

                        return TraefikUiUsername::fromString($answer)->value();
                    }
                )
        );
    }

    private function getTraefikUiPassword(): string
    {
        $this->style->section("Traefik UI password");

        $this->help(
            [
                TraefikUiPassword::HELP
            ]
        );

        return $this->style->askQuestion(
            (new Question(
                "Choose a password for your Traefik UI"
            ))
                ->setNormalizer(
                    function ($answer): string {
                        return trim((string)$answer);
                    }
                )
                ->setValidator(
                    function (string $answer): string {
                        if (!$answer) {
                            throw new \Exception('No answer given. Try again.');
                        }
                        return TraefikUiPassword::fromString($answer)->value();
                    }
                )
        );
    }

    private function getTraefikUiHostname(): string
    {
        $this->style->section("Traefik UI hostname");
        $this->help(
            [
                "This the fully qualified domain name for your Traefik UI.",
                "It has to be with a subdomain like in 'traefik.mydomain.com', in which case 'mydomain.com' must point to ".
                "the name servers of Digital Ocean, and Cocotte will create and configure the 'traefik' subdomain for you.",
                "Cocotte Wizard validates that the name servers of the domain you enter are Digital Ocean's.",
                "How to point to Digital Ocean name servers:\n".
                "www.digitalocean.com/community/tutorials/how-to-point-to-digitalocean-nameservers-from-common-domain-registrars",
                "Please note that when a domain is newly registered, or the name servers are changed, you can expect ".
                "a propagation time up to 24 hours. This is because it takes time for the DNS to take effect across ".
                "the internet. The actual time of propagation may vary in some locations based on your network setup.",
            ]
        );

        return $this->style->askQuestion(
            (new Question(
                "Enter Traefik UI hostname (e.g., traefik.mydomain.com)"
            ))
                ->setNormalizer(
                    function ($answer): string {
                        return trim((string)$answer);
                    }
                )
                ->setValidator(
                    function (string $answer): string {
                        if (!$answer) {
                            throw new \Exception('No answer given. Try again.');
                        }

                        $hostname = Hostname::parse($answer);

                        $this->dnsValidator->validateHost($hostname);

                        $this->style->success("Traefik UI hostname '$hostname' is valid.");
                        $this->style->ask("Press Enter to continue");

                        return $hostname->toString();
                    }
                )
        );
    }

    private function getToken(): string
    {
        $this->style->section("Digital Ocean API Token");
        $this->help(
            [
                "You must provide a Digital Ocean API Token to Cocotte.",
                "If you don't have a Digital Ocean account yet, get one with a 10$ credit at https://m.do.co/c/c25ed78e51c5",
                "Then generate a token at https://cloud.digitalocean.com/settings/api/tokens",
                "Cocotte Wizard will make a call to Digital Ocean's API to validate the token.",
            ]
        );

        return $this->style->askQuestion(
            (new Question(
                "Enter your Digital Ocean API token"
            ))
                ->setNormalizer(
                    function ($answer): string {
                        return trim((string)$answer);
                    }
                )
                ->setValidator(
                    function (string $answer): string {
                        if (!$answer) {
                            throw new \Exception('No answer given. Try again.');
                        }

                        $token = new ApiToken($answer);
                        $token->assertTokenIsValid();

                        $this->style->success("Token is valid");
                        $this->style->ask("Press Enter to continue");

                        return $token->value();
                    }
                )
        );
    }

    private function getProjectPath(): string
    {
        $this->style->section("New project location");
        $this->help(
            [
                "Enter an absolute path on your computer from where to run Cocotte.",
                "If it does not exist, Cocotte will create it. It should be an empty directory where you usually ".
                "put new project code.",
                "The path cannot contain single quotes, double quotes or dollar ".
                "signs. You should also avoid spaces but they're supposed to work.",
            ]
        );

        return $this->style->askQuestion(
            (new Question(
                "Enter the absolute path to your new Cocotte project (e.g., /home/joe/dev/cocotte)"
            ))
                ->setNormalizer(
                    function ($answer): string {
                        return rtrim(trim((string)$answer), '/');
                    }
                )
                ->setValidator(
                    function (string $answer): string {
                        if (!$answer) {
                            throw new \Exception('No answer given. Try again.');
                        }
                        if (preg_match('/[\'"$]/', $answer)) {
                            throw new \Exception("'$answer' contains single quotes, double quotes or dollar signs");
                        }
                        if (!$this->filesystem->isAbsolutePath($answer)) {
                            throw new \Exception("'$answer' is not an absolute path.");
                        }
                        if ($this->style->confirm("Do you confirm this location: '$answer' ?")) {
                            return $answer;
                        } else {
                            throw new \Exception("Cancelled. Enter a new path.");
                        }
                    }
                )
        );
    }

    private function help($message)
    {
        $this->style->block($message, 'HELP');
    }
}