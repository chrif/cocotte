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
use Cocotte\Machine\MachineCreator;
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
        TraefikDeploymentValidator $traefikDeploymentValidator
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
            ->setDescription(
                $description = 'Create a <options=bold>Docker</> machine on <options=bold>Digital Ocean</> and '.
                    'install the <options=bold>Traefik</> reverse proxy on it.')
            ->setHelp(
                $this->formatHelp(
                    $description,
                    'docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte install \
    --digital-ocean-api-token="xxxx" \
    --traefik-ui-hostname="traefik.mydomain.com" \
    --traefik-ui-password="password" \
    --traefik-ui-username="username";'
                )
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->confirm();
        $this->style->writeln("Creating a Docker machine named '{$this->machineName}' on Digital Ocean.");
        $this->machineCreator->create();

        $this->style->writeln("Creating Traefik template in {$this->hostMount->sourcePath()}/traefik");
        $this->traefikCreator->create();

        $this->style->writeln("Configuring networking for {$this->traefikHostname->toString()}");
        $this->networkingConfigurator->configure($this->traefikHostname->toHostnameCollection());

        $this->style->writeln('Deploying Traefik to cloud machine');
//        $this->processRunner->run(new Process('../bin/reset-prod 2>/dev/stdout', $this->traefikCreator->hostAppPath()));
        $this->processRunner->mustRun(new Process('./bin/prod 2>/dev/stdout', $this->traefikCreator->hostAppPath()));

        $this->style->writeln('Waiting for Traefik to start');
        $this->traefikDeploymentValidator->validate();

        $this->processRunner->mustRun(new Process('./bin/logs -t', $this->traefikCreator->hostAppPath()));

        $this->style->complete([
            "Installation successful.",
            "You can now:\n".
            "- visit your Traefik UI at <options=bold>https://{$this->traefikHostname->toString()}</>\n".
            "- use docker-machine commands (e.g. <options=bold>docker-machine -s machine ssh {$this->machineName}</>)\n".
            "- deploy a static website on your cloud machine with the <options=bold>static-site</> Cocotte command.",
        ]);
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

}