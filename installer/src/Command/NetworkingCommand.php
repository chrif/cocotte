<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\AbstractCommand;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Chrif\Cocotte\DigitalOcean\HostnameCollection;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
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
        Style $style
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
            ->addOption('remove', null, InputOption::VALUE_NONE, 'Remove networking for hostnames');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->networkingConfigurator->configure(
            HostnameCollection::fromString($input->getArgument('hostnames')),
            $input->getOption('remove')
        );
        $this->style->success("Networking successfully configured.");
    }

}