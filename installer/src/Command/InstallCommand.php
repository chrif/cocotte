<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\AbstractCommand;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Host\HostMount;
use Chrif\Cocotte\Machine\MachineCreator;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameOptionProvider;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikCreator;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameOptionProvider;
use Chrif\Cocotte\Template\Traefik\TraefikPassword;
use Chrif\Cocotte\Template\Traefik\TraefikPasswordOptionProvider;
use Chrif\Cocotte\Template\Traefik\TraefikUsername;
use Chrif\Cocotte\Template\Traefik\TraefikUsernameOptionProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

final class InstallCommand extends AbstractCommand implements LazyEnvironment
{
    /**
     * @var MachineCreator
     */
    private $machineCreator;

    /**
     * @var TraefikCreator
     */
    private $traefikCreator;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var MachineName
     */
    private $machineName;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var HostMount
     */
    private $hostMount;

    public function __construct(
        MachineCreator $machineCreator,
        TraefikCreator $traefikCreator,
        Style $style,
        MachineName $machineName,
        TraefikHostname $traefikHostname,
        EventDispatcherInterface $eventDispatcher,
        NetworkingConfigurator $networkingConfigurator,
        ProcessRunner $processRunner,
        HostMount $hostMount
    ) {
        $this->machineCreator = $machineCreator;
        $this->traefikCreator = $traefikCreator;
        $this->style = $style;
        $this->machineName = $machineName;
        $this->traefikHostname = $traefikHostname;
        $this->eventDispatcher = $eventDispatcher;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->processRunner = $processRunner;
        $this->hostMount = $hostMount;
        parent::__construct();
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
            MachineName::class,
            MachineStoragePath::class,
            TraefikHostname::class,
            TraefikPassword::class,
            TraefikUsername::class,
        ];
    }

    public function optionProviders(): array
    {
        return [
            ApiTokenOptionProvider::class,
            MachineNameOptionProvider::class,
            TraefikHostnameOptionProvider::class,
            TraefikUsernameOptionProvider::class,
            TraefikPasswordOptionProvider::class,
        ];
    }

    protected function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function doConfigure(): void
    {
        $this
            ->setName('install')
            ->setDescription('Create a <options=bold>Docker</> machine on <options=bold>Digital Ocean</> and '.
                'install the <options=bold>Traefik</> reverse proxy on it.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->confirm();
        $this->style->writeln("Creating a Docker machine named '{$this->machineName}' on Digital Ocean.");
        $this->machineCreator->create();

        $this->style->writeln("Exporting traefik template to {$this->hostMount->sourcePath()}/traefik");
        $this->traefikCreator->create();

        $this->style->writeln("Configuring networking for {$this->traefikHostname->toString()}");
        $this->networkingConfigurator->configure($this->traefikHostname->toHostnameCollection());

        $this->style->writeln('Deploying traefik to cloud machine');
        $this->processRunner->mustRun(new Process('./bin/prod 2>/dev/stdout', $this->traefikCreator->hostAppPath()));

        $this->style->success("Installation successful. You can visit your Traefik UI at {$this->traefikHostname->formatSecureUrl()}");
    }

    private function confirm(): void
    {
        if (!$this->style->confirm(
            "You are about to create a Docker machine named '<options=bold>{$this->machineName->toString()}</>' " .
            "on Digital Ocean \nand install the Traefik reverse proxy on it with hostname(s) " .
            "'<options=bold>{$this->traefikHostname->toString()}</>'.\n".
            "This action may take a few minutes."
        )) {
            throw new \Exception('Cancelled');
        };
    }

}