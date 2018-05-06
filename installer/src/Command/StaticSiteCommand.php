<?php declare(strict_types=1);

namespace Cocotte\Command;

use Cocotte\Console\AbstractCommand;
use Cocotte\Console\DocumentedCommand;
use Cocotte\Console\Style;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Cocotte\DigitalOcean\NetworkingConfigurator;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Host\HostMount;
use Cocotte\Host\HostMountRequired;
use Cocotte\Machine\MachineIp;
use Cocotte\Machine\MachineName;
use Cocotte\Machine\MachineNameOptionProvider;
use Cocotte\Machine\MachineRequired;
use Cocotte\Machine\MachineState;
use Cocotte\Machine\MachineStoragePath;
use Cocotte\Shell\ProcessRunner;
use Cocotte\Template\StaticSite\StaticSiteCreator;
use Cocotte\Template\StaticSite\StaticSiteDeploymentValidator;
use Cocotte\Template\StaticSite\StaticSiteHostname;
use Cocotte\Template\StaticSite\StaticSiteHostnameOptionProvider;
use Cocotte\Template\StaticSite\StaticSiteNamespace;
use Cocotte\Template\StaticSite\StaticSiteNamespaceOptionProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

final class StaticSiteCommand extends AbstractCommand implements
    LazyEnvironment,
    HostMountRequired,
    DocumentedCommand,
    MachineRequired
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
    /**
     * @var StaticSiteDeploymentValidator
     */
    private $staticSiteDeploymentValidator;
    /**
     * @var MachineIp
     */
    private $machineIp;

    /**
     * @codeCoverageIgnore
     * @param StaticSiteCreator $staticSiteCreator
     * @param NetworkingConfigurator $networkingConfigurator
     * @param StaticSiteHostname $staticSiteHostname
     * @param Style $style
     * @param ProcessRunner $processRunner
     * @param EventDispatcherInterface $eventDispatcher
     * @param MachineState $machineState
     * @param StaticSiteNamespace $staticSiteNamespace
     * @param HostMount $hostMount
     * @param StaticSiteDeploymentValidator $staticSiteDeploymentValidator
     * @param MachineIp $machineIp
     */
    public function __construct(
        StaticSiteCreator $staticSiteCreator,
        NetworkingConfigurator $networkingConfigurator,
        StaticSiteHostname $staticSiteHostname,
        Style $style,
        ProcessRunner $processRunner,
        EventDispatcherInterface $eventDispatcher,
        MachineState $machineState,
        StaticSiteNamespace $staticSiteNamespace,
        HostMount $hostMount,
        StaticSiteDeploymentValidator $staticSiteDeploymentValidator,
        MachineIp $machineIp
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
        $this->staticSiteDeploymentValidator = $staticSiteDeploymentValidator;
        parent::__construct();
        $this->machineIp = $machineIp;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
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

    /**
     * @codeCoverageIgnore
     * @return array
     */
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
                'Do not configure networking. Cannot be true if skip-deploy is true.')
            ->addOption(
                'skip-deploy',
                null,
                InputOption::VALUE_NONE,
                'Do not deploy to prod after creation.')
            ->setDescription($description = 'Create a static website and deploy it to your Docker Machine.')
            ->setHelp(
                $this->formatHelp($description, $this->example())
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->confirm();

        $skipNetworking = $input->getOption('skip-networking');
        $skipDeploy = $input->getOption('skip-deploy');

        if ($skipNetworking && !$skipDeploy) {
            throw new \Exception("Cannot skip networking when deploying");
        }

        $this->style->writeln(
            "Creating a new static site in {$this->sitePath()}"
        );
        $this->staticSiteCreator->create();

        if (!$skipNetworking) {
            $this->style->writeln("Configuring networking for {$this->staticSiteHostname}");
            $this->networkingConfigurator->configure(
                $this->staticSiteHostname->toHostnameCollection(),
                $this->machineIp->toIP()
            );
        }

        if (!$skipDeploy) {
            $this->style->writeln('Deploying created site to cloud machine');
            $this->processRunner->mustRun(new Process('./bin/prod 2>/dev/stdout',
                $this->staticSiteCreator->hostAppPath()));

            $this->style->writeln('Waiting for site to respond');
            $this->staticSiteDeploymentValidator->validate();

            $this->processRunner->mustRun(new Process('./bin/logs -t', $this->staticSiteCreator->hostAppPath()));

            $this->style->complete(
                [
                    "Static site successfully deployed at ".
                    "<options=bold>https://{$this->staticSiteHostname->toString()}</>",
                ]
            );
        } else {
            $this->style->complete("Deployment has been skipped.");
        }
    }

    private function sitePath(): string
    {
        return "{$this->hostMount->sourcePath()}/{$this->staticSiteNamespace}";
    }

    private function confirm(): void
    {
        if (!$this->style->confirm($this->confirmMessage())) {
            throw new \Exception('Cancelled');
        };
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    private function example(): string
    {
        return <<<'TAG'
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte static-site \
    --digital-ocean-api-token="xxxx" \
    --namespace="static-site" \
    --hostname="static-site.mydomain.com";
TAG;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    private function confirmMessage(): string
    {
        return "You are about to create a static website in ".
            "'<options=bold>{$this->sitePath()}</>'\n".
            " and deploy it to Digital Ocean at ".
            "'<options=bold>{$this->staticSiteHostname->toString()}</>'.";
    }

}
