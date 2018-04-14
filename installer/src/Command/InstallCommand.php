<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Machine\MachineCreator;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikExporter;
use Chrif\Cocotte\Template\Traefik\TraefikUiHostname;
use Chrif\Cocotte\Template\Traefik\TraefikUiPassword;
use Chrif\Cocotte\Template\Traefik\TraefikUiUsername;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class InstallCommand extends Command
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

    public function __construct(
        ApiToken $token,
        MachineStoragePath $machineStoragePath,
        EnvironmentManager $environmentManager,
        MachineCreator $machineCreator,
        ProcessRunner $processRunner,
        MachineState $machineState,
        TraefikExporter $traefikExporter,
        Style $style
    ) {
        $this->token = $token;
        $this->machineStoragePath = $machineStoragePath;
        $this->environmentManager = $environmentManager;
        $this->machineCreator = $machineCreator;
        $this->processRunner = $processRunner;
        $this->machineState = $machineState;
        $this->traefikExporter = $traefikExporter;
        $this->style = $style;
        parent::__construct();
    }

    public function isEnabled()
    {
        return !$this->machineState->exists();
    }

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Create a Docker Machine on Digital Ocean and install the Traefik reverse proxy on it')
            ->getDefinition()->addOptions(
                [
                    ApiToken::inputOption(),
                    MachineStoragePath::inputOption(),
                    MachineName::inputOption(),
                    TraefikUiHostname::inputOption(),
                    TraefikUiPassword::inputOption(),
                    TraefikUiUsername::inputOption(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->exportFromInput($input);
        $this->machineCreator->create();
        $this->traefikExporter->export();
        $this->style->title('Deploying exported site to cloud machine');
        $this->processRunner->mustRun(new Process('./bin/prod', $this->traefikExporter->hostAppPath()));
    }

}