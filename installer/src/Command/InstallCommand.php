<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiTokenInteraction;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Machine\MachineCreator;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameInteraction;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikExporter;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameInteraction;
use Chrif\Cocotte\Template\Traefik\TraefikPasswordInteraction;
use Chrif\Cocotte\Template\Traefik\TraefikUsernameInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class InstallCommand extends Command
{
    /**
     * @var MachineStoragePath
     */
    private $machineStoragePath;

    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * @var MachineCreator
     */
    private $machineCreator;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var MachineState
     */
    private $machineState;

    /**
     * @var TraefikExporter
     */
    private $traefikExporter;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var MachineName
     */
    private $machineName;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var TraefikPasswordInteraction
     */
    private $traefikPasswordInteraction;

    /**
     * @var TraefikHostnameInteraction
     */
    private $traefikHostnameInteraction;

    /**
     * @var TraefikUsernameInteraction
     */
    private $traefikUsernameInteraction;

    /**
     * @var ApiTokenInteraction
     */
    private $apiTokenInteraction;

    /**
     * @var MachineNameInteraction
     */
    private $machineNameInteraction;

    public function __construct(
        MachineStoragePath $machineStoragePath,
        EnvironmentManager $environmentManager,
        MachineCreator $machineCreator,
        ProcessRunner $processRunner,
        MachineState $machineState,
        TraefikExporter $traefikExporter,
        Style $style,
        NetworkingConfigurator $networkingConfigurator,
        TraefikHostname $traefikHostname,
        TraefikPasswordInteraction $traefikPasswordInteraction,
        TraefikHostnameInteraction $traefikHostnameInteraction,
        TraefikUsernameInteraction $traefikUsernameInteraction,
        ApiTokenInteraction $apiTokenInteraction,
        MachineNameInteraction $machineNameInteraction
    ) {
        $this->machineStoragePath = $machineStoragePath;
        $this->environmentManager = $environmentManager;
        $this->machineCreator = $machineCreator;
        $this->processRunner = $processRunner;
        $this->machineState = $machineState;
        $this->traefikExporter = $traefikExporter;
        $this->style = $style;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->traefikHostname = $traefikHostname;
        $this->traefikPasswordInteraction = $traefikPasswordInteraction;
        $this->traefikHostnameInteraction = $traefikHostnameInteraction;
        $this->traefikUsernameInteraction = $traefikUsernameInteraction;
        $this->apiTokenInteraction = $apiTokenInteraction;
        $this->machineNameInteraction = $machineNameInteraction;
        parent::__construct();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->apiTokenInteraction->interact($input);
        $this->machineNameInteraction->interact($input);
        $this->traefikHostnameInteraction->interact($input);
        $this->traefikUsernameInteraction->interact($input);
        $this->traefikPasswordInteraction->interact($input);
    }

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Create a Docker Machine on Digital Ocean and install the Traefik reverse proxy on it.')
            ->getDefinition()->addOptions(
                [
                    $this->apiTokenInteraction->option(),
                    $this->traefikHostnameInteraction->option(),
                    $this->traefikUsernameInteraction->option(),
                    $this->traefikPasswordInteraction->option(),
                    $this->machineNameInteraction->option(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->exportFromInput($input);
        $this->machineStoragePath->export();

        $install = $input->getOption('no-interaction') || $this->style->confirm(
                "You are about to create a Docker Machine named '{$this->machineName}' on Digital Ocean ".
                "and install the Traefik reverse proxy on it with hostname '{$this->traefikHostname}'".
                "This action may take a few minutes."
            );

        if ($install) {
            $this->machineCreator->create();
            $this->traefikExporter->export();
            $this->networkingConfigurator->configure($this->traefikHostname->toHostnameCollection());
            $this->style->title('Deploying exported site to cloud machine');
            $this->processRunner->mustRun(new Process('./bin/prod', $this->traefikExporter->hostAppPath()));
        }
    }
}