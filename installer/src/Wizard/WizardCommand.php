<?php declare(strict_types=1);

namespace Chrif\Cocotte\Wizard;

use Chrif\Cocotte\Configuration\AppHost;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\DnsValidator;
use Chrif\Cocotte\Filesystem\Filesystem;
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

    public function __construct(Style $style, Filesystem $filesystem, DnsValidator $dsnValidator)
    {
        $this->style = $style;
        $this->filesystem = $filesystem;
        $this->dnsValidator = $dsnValidator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('wizard')
            ->setDescription('Interactively build a simple command to run Cocotte');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $this->style->title("Cocotte Wizard");
        $this->help(
            [
                "This wizard helps you get started with Cocotte.",
                "It assumes that you own a domain name and can change its name servers.",
                "Visit https://github.com/chrif/cocotte for Cocotte documentation.",
                "Press CTRL+D at any moment to quit.",
            ]
        );
        $this->style->ask("Press Enter to continue");

        $token = $this->getToken();
        $projectPath = $this->getProjectPath();
        $traefikUiHost = $this->getTraefikUiHost();
        $traefikUiUsername = $this->getTraefikUiUsername();
        $traefikUiPassword = $this->getTraefikUiPassword();

        $projectPath = str_replace(" ", "\ ", $projectPath);
        $this->style->writeln(
            <<<EOF
Run this command to let Cocotte create a cloud machine for you:
---------------------------------------------------------------
mkdir -p $projectPath && cd $projectPath && docker run -it --rm \
-e DIGITAL_OCEAN_API_TOKEN="$token" \
-e MACHINE_STORAGE_PATH="$(pwd)/machine" \
-e TRAEFIK_UI_HOST="$traefikUiHost" \
-e TRAEFIK_UI_PASSWORD="$traefikUiPassword" \
-e TRAEFIK_UI_USERNAME="$traefikUiUsername" \
-v "$(pwd)":/host \
chrif/cocotte install;
---------------------------------------------------------------
EOF
        );
    }

    private function getTraefikUiUsername(): string
    {
        $this->style->section("Traefik UI username");

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

                        return $answer;
                    }
                )
        );
    }

    private function getTraefikUiPassword(): string
    {
        $this->style->section("Traefik UI password");

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

                        return $answer;
                    }
                )
        );
    }

    private function getTraefikUiHost(): string
    {
        $this->style->section("Traefik UI domain");
        $this->help(
            [
                "This the fully qualified domain name for your Traefik UI.",
                "It has to be with a subdomain like in 'traefik.mydomain.com', in which case 'mydomain.com' must point to ".
                "the nameservers of Digital Ocean, and Cocotte will create and configure the 'traefik' subdomain for you.",
                "How to point to Digital Ocean name servers:\n".
                "www.digitalocean.com/community/tutorials/how-to-point-to-digitalocean-nameservers-from-common-domain-registrars",
            ]
        );

        return $this->style->askQuestion(
            (new Question(
                "Enter Traefik UI domain (e.g., traefik.mydomain.com)"
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

                        $host = AppHost::parse($answer);

                        $this->dnsValidator->validateHost($host);

                        $this->style->success("Traefik UI domain '$host' is valid.");

                        return $host->toString();
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

                        $adapter = new GuzzleHttpAdapter($answer);
                        $digitalOceanV2 = new DigitalOceanV2($adapter);
                        $account = $digitalOceanV2->account()->getUserInformation();
                        if ($account->status === 'active') {
                            $this->style->success("Token is valid");
                        } else {
                            throw new \Exception(
                                "Token works but is associated to an account with status '{$account->status}'."
                            );
                        }

                        return $answer;
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
                "It should be an empty directory where you usually put new project code.",
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
                        return trim((string)$answer);
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