<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Machine\MachineCreator;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikUiHostname;
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
     * @var MachineCreator
     */
    private $machineCreator;

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
     * @var TraefikUiHostname
     */
    private $traefikUiHostname;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var MachineName
     */
    private $machineName;

    public function __construct(
        EnvironmentManager $environmentManager,
        MachineCreator $machineCreator,
        ProcessRunner $processRunner,
        NetworkingConfigurator $networkingConfigurator,
        MachineState $machineState,
        TraefikUiHostname $traefikUiHostname,
        Style $style,
        MachineName $machineName
    ) {
        $this->environmentManager = $environmentManager;
        $this->machineCreator = $machineCreator;
        $this->processRunner = $processRunner;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->machineState = $machineState;
        $this->traefikUiHostname = $traefikUiHostname;
        $this->style = $style;
        $this->machineName = $machineName;
        parent::__construct();
    }

    public function isHidden()
    {
        return !$this->machineState->exists();
    }

    protected function configure()
    {
        $this
            ->setName('uninstall')
            ->setDescription('Destroy the Docker Machine on Digital Ocean and remove the Traefik subdomain.')
            ->getDefinition()
            ->addOptions(
                [
                    TraefikUiHostname::inputOption(),
                    ApiToken::inputOption(),
                    MachineStoragePath::inputOption(),
                    MachineName::inputOption(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uninstall = $input->getOption('no-interaction') || $this->style->confirm(
            "You are about to uninstall a Docker Machine named '{$this->machineName}' on Digital Ocean and ".
            "remove the domain record '{$this->traefikUiHostname}' associated with this machine.",
            false
        );
        if ($uninstall) {
            $this->environmentManager->exportFromInput($input);
            $this->networkingConfigurator->configure(
                $this->traefikUiHostname->toHostnameCollection(),
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