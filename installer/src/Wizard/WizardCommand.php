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

    public function __construct(Style $style, Filesystem $filesystem)
    {
        $this->style = $style;
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('wizard')
            ->setDescription('Interactively build the command to run Cocotte');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $this->style->title("Cocotte Wizard");
        $this->help(
            [
                "This wizard helps you get started with Cocotte.",
                "It assumes that you own a domain name and can change its name servers.",
                "It does not save the information it collects.",
                "Visit https://github.com/chrif/cocotte for Cocotte documentation.",
                "Press CTRL+D at any moment to quit.",
            ]
        );
        $this->style->ask("Press Enter to continue");

        // required
        $token = $this->getToken();
        $projectPath = $this->getProjectPath();
        $traefikUiHost = $this->getTraefikUiHost();
    }

    private function getTraefikUiHost()
    {
        $this->style->section("Traefik UI domain");
        $this->help(
            [
                "This the fully qualified domain name for your Traefik UI.",
                "The nameservers of the domain must point to Digital Ocean. How to:",
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

                        try {
                            $dnsValidator = new DnsValidator();
                            $dnsValidator->validate($host);
                        } catch (\Exception $e) {
                            throw new \Exception(
                                "Failed to validate name servers of '$host' with message:\n".$e->getMessage()
                            );
                        }

                        $this->style->success("Traefik UI domain '$host' is valid.");

                        return $host->toString();
                    }
                )
        );
    }

    private function getToken()
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
                                "Token works but is associated to an account with status '{$account->status}'. ".
                                "Account email is ".$account->email
                            );
                        }

                        return $answer;
                    }
                )
        );
    }

    private function getProjectPath()
    {
        $this->style->section("New project location");
        $this->help(
            [
                "Enter an absolute path on your computer from where to run Cocotte.",
                "It should be an empty directory where you usually put project code.",
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