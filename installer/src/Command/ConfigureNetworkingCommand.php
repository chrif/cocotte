<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Configuration\AppHostCollection;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ConfigureNetworkingCommand extends Command
{
    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    public function __construct(NetworkingConfigurator $networkingConfigurator)
    {
        $this->networkingConfigurator = $networkingConfigurator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cocotte:configure-networking')
            ->setDescription('Configure networking of Digital Ocean')
            ->addArgument('hosts', InputArgument::REQUIRED, 'Comma-separated list of hosts')
            ->addOption('remove', null, InputOption::VALUE_NONE, 'Remove networking for hosts')
            ->setAliases(['cn']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->networkingConfigurator->configure(
            AppHostCollection::fromString($input->getArgument('hosts')),
            $input->getOption('remove')
        );
    }
}