<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiTokenInteraction;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameInteraction;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class UninstallCommand extends Command
{
    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var MachineState
     */
    private $machineState;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var MachineName
     */
    private $machineName;

    /**
     * @var MachineStoragePath
     */
    private $machineStoragePath;

    /**
     * @var TraefikHostnameInteraction
     */
    private $traefikHostnameInteraction;

    /**
     * @var ApiTokenInteraction
     */
    private $apiTokenInteraction;

    /**
     * @var MachineNameInteraction
     */
    private $machineNameInteraction;

    public function __construct(
        EnvironmentManager $environmentManager,
        ProcessRunner $processRunner,
        NetworkingConfigurator $networkingConfigurator,
        MachineState $machineState,
        TraefikHostname $traefikHostname,
        Style $style,
        MachineName $machineName,
        MachineStoragePath $machineStoragePath,
        TraefikHostnameInteraction $traefikHostnameInteraction,
        ApiTokenInteraction $apiTokenInteraction,
        MachineNameInteraction $machineNameInteraction
    ) {
        $this->environmentManager = $environmentManager;
        $this->processRunner = $processRunner;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->machineState = $machineState;
        $this->traefikHostname = $traefikHostname;
        $this->style = $style;
        $this->machineName = $machineName;
        $this->machineStoragePath = $machineStoragePath;
        $this->traefikHostnameInteraction = $traefikHostnameInteraction;
        $this->apiTokenInteraction = $apiTokenInteraction;
        $this->machineNameInteraction = $machineNameInteraction;
        parent::__construct();
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->apiTokenInteraction->interact($input);
        $this->machineNameInteraction->interact($input);
        $this->traefikHostnameInteraction->interact($input);
    }

    protected function configure()
    {
        $this
            ->setName('uninstall')
            ->setDescription('Destroy the Docker Machine on Digital Ocean and remove the Traefik subdomain.')
            ->getDefinition()
            ->addOptions(
                [
                    $this->apiTokenInteraction->option(),
                    $this->machineNameInteraction->option(),
                    $this->traefikHostnameInteraction->option(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->exportFromInput($input);
        $this->machineStoragePath->export();

        $uninstall = $input->getOption('no-interaction') || $this->style->confirm(
                "You are about to uninstall a Docker Machine named '<options=bold>{$this->machineName}</>' on Digital Ocean and ".
                "remove the domain record '<options=bold>{$this->traefikHostname}</>' associated with this machine.",
                false
            );
        if ($uninstall) {
            $this->networkingConfigurator->configure(
                $this->traefikHostname->toHostnameCollection(),
                true
            );
            $this->processRunner->mustRun(
                new Process(
                    'docker-machine rm -y "${MACHINE_NAME}"'
                )
            );
        } else {
            $this->style->writeln('Cancelled');
        }
    }
}