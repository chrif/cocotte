<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\AppHostCollection;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Machine\MachineCreator;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikUiHost;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class UninstallCommand extends Command
{
    /**
     * @var \Chrif\Cocotte\DigitalOcean\ApiToken
     */
    private $token;

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
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var MachineState
     */
    private $machineState;

    public function __construct(
        ApiToken $token,
        MachineStoragePath $machineStoragePath,
        EnvironmentManager $environmentManager,
        MachineCreator $machineCreator,
        ProcessRunner $processRunner,
        NetworkingConfigurator $networkingConfigurator,
        MachineState $machineState
    ) {
        $this->token = $token;
        $this->machineStoragePath = $machineStoragePath;
        $this->environmentManager = $environmentManager;
        $this->machineCreator = $machineCreator;
        $this->processRunner = $processRunner;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->machineState = $machineState;
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
                    TraefikUiHost::inputOption(),
                    ApiToken::inputOption(),
                    MachineStoragePath::inputOption(),
                    MachineName::inputOption(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->exportFromInput($input);
        $this->networkingConfigurator->configure(
            AppHostCollection::fromString($input->getOption('traefik-ui-host')),
            true
        );
        $this->processRunner->mustRun(
            new Process(
                'docker-machine -s "${MACHINE_STORAGE_PATH}" rm -y "${COCOTTE_MACHINE}"'
            )
        );
    }
}