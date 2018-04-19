<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\AbstractCommand;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Host\HostMount;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameOptionProvider;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\StaticSite\StaticSiteCreator;
use Chrif\Cocotte\Template\StaticSite\StaticSiteHostname;
use Chrif\Cocotte\Template\StaticSite\StaticSiteHostnameOptionProvider;
use Chrif\Cocotte\Template\StaticSite\StaticSiteNamespace;
use Chrif\Cocotte\Template\StaticSite\StaticSiteNamespaceOptionProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

final class StaticSiteCommand extends AbstractCommand implements LazyEnvironment
{
    /**
     * @var StaticSiteCreator
     */
    private $staticSiteCreator;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var StaticSiteHostname
     */
    private $staticSiteHostname;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MachineState
     */
    private $machineState;
    /**
     * @var StaticSiteNamespace
     */
    private $staticSiteNamespace;
    /**
     * @var HostMount
     */
    private $hostMount;

    public function __construct(
        StaticSiteCreator $staticSiteCreator,
        NetworkingConfigurator $networkingConfigurator,
        StaticSiteHostname $staticSiteHostname,
        Style $style,
        ProcessRunner $processRunner,
        EventDispatcherInterface $eventDispatcher,
        MachineState $machineState,
        StaticSiteNamespace $staticSiteNamespace,
        HostMount $hostMount
    ) {
        $this->staticSiteCreator = $staticSiteCreator;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->staticSiteHostname = $staticSiteHostname;
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->eventDispatcher = $eventDispatcher;
        $this->machineState = $machineState;
        $this->staticSiteNamespace = $staticSiteNamespace;
        $this->hostMount = $hostMount;
        parent::__construct();
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
            MachineName::class,
            MachineStoragePath::class,
            StaticSiteNamespace::class,
            StaticSiteHostname::class,
        ];
    }

    public function optionProviders(): array
    {
        return [
            StaticSiteNamespaceOptionProvider::class,
            StaticSiteHostnameOptionProvider::class,
            ApiTokenOptionProvider::class,
            MachineNameOptionProvider::class,
        ];
    }

    protected function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function doConfigure(): void
    {
        $this
            ->setName('static-site')
            ->addOption(
                'skip-networking',
                null,
                InputOption::VALUE_NONE,
                'Configure networking. Cannot be true if skip-deploy is true'
            )
            ->addOption(
                'skip-deploy',
                null,
                InputOption::VALUE_NONE,
                'Deploy to prod after exportation'
            )
            ->setDescription('Create a static website and deploy it to your Docker Machine.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->machineState->exists()) {
            $this->style->warning("Could not find a machine. ".
                "Did you create a machine with the install command before ? ".
                "Did you provide the correct machine name ?");
        }

        $skipNetworking = $input->getOption('skip-networking');
        $skipDeploy = $input->getOption('skip-deploy');

        if ($skipNetworking && !$skipDeploy) {
            throw new \Exception("Cannot skip networking when deploying");
        }

        $this->style->writeln(
            "Exporting a new static site to {$this->hostMount->sourcePath()}/{$this->staticSiteNamespace}"
        );
        $this->staticSiteCreator->create();

        if (!$skipNetworking) {
            $this->style->writeln("Configuring networking for {$this->staticSiteHostname->toString()}");
            $this->networkingConfigurator->configure($this->staticSiteHostname->toHostnameCollection());
        }

        if (!$skipDeploy) {
            $this->style->writeln('Deploying exported site to cloud machine');
            $this->processRunner->mustRun(new Process('./bin/prod 2>/dev/stdout',
                $this->staticSiteCreator->hostAppPath()));
            $this->style->success("Static site successfully deployed at {$this->staticSiteHostname->formatSecureUrl()}");
        } else {
            $this->style->success("Deployment has been skipped.");
        }
    }

}
