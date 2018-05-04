<?php declare(strict_types=1);

namespace Cocotte\Command;

use Cocotte\Console\AbstractCommand;
use Cocotte\Console\Style;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Cocotte\DigitalOcean\HostnameCollection;
use Cocotte\DigitalOcean\NetworkingConfigurator;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Machine\MachineIp;
use Darsyn\IP\IP;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class NetworkingCommand extends AbstractCommand implements LazyEnvironment
{
    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Style
     */
    private $style;

    public function __construct(
        NetworkingConfigurator $networkingConfigurator,
        EventDispatcherInterface $eventDispatcher,
        Style $style,
        MachineIp $machineIp
    ) {
        $this->networkingConfigurator = $networkingConfigurator;
        $this->eventDispatcher = $eventDispatcher;
        $this->style = $style;
        parent::__construct();
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
        ];
    }

    public function optionProviders(): array
    {
        return [
            ApiTokenOptionProvider::class,
        ];
    }

    public function isHidden()
    {
        return !getenv('SHOW_HIDDEN_COMMANDS');
    }

    protected function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function doConfigure(): void
    {
        $this
            ->setName('networking')
            ->setDescription('Configure networking of Digital Ocean')
            ->addArgument('hostnames', InputArgument::REQUIRED, 'Comma-separated list of hostnames')
            ->addOption('ip',
                null,
                InputOption::VALUE_REQUIRED,
                'IP to use for hostnames (required without the --remove option)')
            ->addOption('remove', null, InputOption::VALUE_NONE, 'Remove networking for hostnames');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $hostnames = HostnameCollection::fromString($input->getArgument('hostnames'));

        if ($input->getOption('remove')) {
            $this->networkingConfigurator->remove($hostnames);
            $this->style->success("Networking successfully removed.");
        } else {
            $ip = new IP($input->getOption('ip'));
            $this->networkingConfigurator->configure($hostnames, $ip);
            $this->style->success("Networking successfully configured.");
        }
    }

}