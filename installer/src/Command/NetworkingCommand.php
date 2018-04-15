<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\HostnameCollection;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class NetworkingCommand extends Command
{
    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    public function __construct(NetworkingConfigurator $networkingConfigurator, EnvironmentManager $environmentManager)
    {
        $this->networkingConfigurator = $networkingConfigurator;
        $this->environmentManager = $environmentManager;
        parent::__construct();
    }

    public function isHidden()
    {
        return !getenv('SHOW_HIDDEN_COMMANDS');
    }

    protected function configure()
    {
        $this
            ->setName('networking')
            ->setDescription('Configure networking of Digital Ocean')
            ->addArgument('hostnames', InputArgument::REQUIRED, 'Comma-separated list of hostnames')
            ->addOption('remove', null, InputOption::VALUE_NONE, 'Remove networking for hostnames')
            ->getDefinition()->addOptions(
                [
                    ApiToken::inputOption(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->exportFromInput($input);
        $this->networkingConfigurator->configure(
            HostnameCollection::fromString($input->getArgument('hostnames')),
            $input->getOption('remove')
        );
    }
}