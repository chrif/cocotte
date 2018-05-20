<?php declare(strict_types=1);

namespace Cocotte\Command;

use Cocotte\Console\AbstractCommand;
use Cocotte\Console\DocumentedCommand;
use Cocotte\Console\Style;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Cocotte\DigitalOcean\NetworkingConfigurator;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Help\DefaultExamples;
use Cocotte\Help\FromEnvExamples;
use Cocotte\Host\HostMount;
use Cocotte\Host\HostMountRequired;
use Cocotte\Machine\MachineCreator;
use Cocotte\Machine\MachineIp;
use Cocotte\Machine\MachineName;
use Cocotte\Machine\MachineNameOptionProvider;
use Cocotte\Machine\MachineStoragePath;
use Cocotte\Shell\ProcessRunner;
use Cocotte\Template\Traefik\TraefikCreator;
use Cocotte\Template\Traefik\TraefikDeploymentValidator;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikHostnameOptionProvider;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikPasswordOptionProvider;
use Cocotte\Template\Traefik\TraefikUsername;
use Cocotte\Template\Traefik\TraefikUsernameOptionProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

final class InstallCommand extends AbstractCommand implements LazyEnvironment, HostMountRequired, DocumentedCommand
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

    /**
     * @var TraefikDeploymentValidator
     */
    private $traefikDeploymentValidator;

    /**
     * @var MachineIp
     */
    private $machineIp;
    /**
     * @var FromEnvExamples
     */
    private $fromEnvExamples;

    public function __construct(
        MachineCreator $machineCreator,
        TraefikCreator $traefikCreator,
        Style $style,
        MachineName $machineName,
        TraefikHostname $traefikHostname,
        EventDispatcherInterface $eventDispatcher,
        NetworkingConfigurator $networkingConfigurator,
        ProcessRunner $processRunner,
        HostMount $hostMount,
        TraefikDeploymentValidator $traefikDeploymentValidator,
        MachineIp $machineIp,
        FromEnvExamples $fromEnvExamples
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
        $this->traefikDeploymentValidator = $traefikDeploymentValidator;
        $this->machineIp = $machineIp;
        $this->fromEnvExamples = $fromEnvExamples;
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
        $this->setName('install')
            ->addOption('dry-run',
                null,
                InputOption::VALUE_NONE,
                'Validate all options but do not proceed with installation.')
            ->setDescription($this->description())
            ->setHelp(
                $this->formatHelp($this->description(),
                    (new DefaultExamples)->install(),
                    (new DefaultExamples)->installInteractive()
                )
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dry-run')) {
            $this->style->writeln(
                "Would have created a Docker machine named '{$this->machineName}' on Digital Ocean."
            );

            return;
        }

        $this->confirm();
        $this->style->writeln("Creating a Docker machine named '{$this->machineName}' on Digital Ocean.");
        $this->machineCreator->create();

        $this->style->writeln("Creating Traefik template in {$this->hostMount->sourcePath()}/traefik");
        $this->traefikCreator->create();

        $this->style->writeln("Configuring networking for {$this->traefikHostname->toString()}");
        $this->networkingConfigurator->configure(
            $this->traefikHostname->toHostnameCollection(),
            $this->machineIp->toIP()
        );

        $this->style->writeln('Deploying Traefik to cloud machine');
//        $this->processRunner->run(new Process('./bin/reset-prod 2>/dev/stdout', $this->traefikCreator->hostAppPath()));
        $this->processRunner->mustRun(new Process('./bin/prod 2>/dev/stdout', $this->traefikCreator->hostAppPath()));

        $this->style->writeln('Waiting for Traefik to start');
        $this->traefikDeploymentValidator->validate();

        $this->processRunner->mustRun(new Process('./bin/logs -t', $this->traefikCreator->hostAppPath()));

        $this->style->complete($this->completeMessage());

        $this->style->writeln($this->command());
    }

    private function confirm(): void
    {
        if (!$this->style->confirm(
            "You are about to create a Docker machine named '<options=bold>{$this->machineName->toString()}</>' on Digital Ocean \n".
            " and install the Traefik reverse proxy on it with hostname '<options=bold>{$this->traefikHostname->toString()}</>'.\n".
            " This action may take a few minutes."
        )) {
            throw new \Exception('Cancelled');
        };
    }

    private function description(): string
    {
        return 'Create a <options=bold>Docker</> machine on <options=bold>Digital Ocean</> and '.
            'install the <options=bold>Traefik</> reverse proxy on it.';
    }

    private function completeMessage(): array
    {
        return [
            "Installation successful.",
            "You can now:\n".
            "- Visit your Traefik UI at <options=bold>https://{$this->traefikHostname->toString()}</>\n".
            "- Use docker-machine commands (e.g. <options=bold>docker-machine -s machine ssh {$this->machineName}</>)\n".
            "- Deploy a static website to your cloud machine with the command below.",
        ];
    }

    private function command(): string
    {
        $command = $this->fromEnvExamples->staticSite(
            null,
            'site1',
            'site1.'.$this->traefikHostname->domainName()
        );

        return <<<EOF
<options=bold,underscore>Run this command to create a static site:</>
{$command}

EOF;
    }
}