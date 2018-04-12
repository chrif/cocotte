<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\AppHostCollection;
use Chrif\Cocotte\Template\AppName;
use Chrif\Cocotte\Template\StaticSite\StaticExporter;
use Chrif\Cocotte\Template\StaticSite\StaticExporterConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class AddSiteCommand extends Command
{
    /**
     * @var StaticExporter
     */
    private $staticExporter;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * @var MachineState
     */
    private $machineState;

    public function __construct(
        StaticExporter $staticTemplateExporter,
        NetworkingConfigurator $networkingConfigurator,
        ProcessRunner $processRunner,
        EnvironmentManager $environmentManager,
        MachineState $machineState
    ) {
        $this->staticExporter = $staticTemplateExporter;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->processRunner = $processRunner;
        $this->environmentManager = $environmentManager;
        $this->machineState = $machineState;
        parent::__construct();
    }

    public function isEnabled()
    {
        return $this->machineState->exists();
    }

    protected function configure()
    {
        $this
            ->setName('add-site')
            ->addArgument(
                'app-name',
                InputArgument::REQUIRED,
                'Name for the exported website. Must be a valid directory name on the host.'
            )
            ->addArgument(
                'app-hosts',
                InputArgument::REQUIRED,
                'Comma-separated list of host(s) for the deployed website.'
            )
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

        $this->getDefinition()->addOption(ApiToken::inputOption());
        $this->getDefinition()->addOption(MachineStoragePath::inputOption());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->exportFromInput($input);

        $skipNetworking = $input->getOption('skip-networking');
        $skipDeploy = $input->getOption('skip-deploy');

        if ($skipNetworking && !$skipDeploy) {
            throw new \Exception("Cannot skip networking when deploying");
        }

        $config = StaticExporterConfiguration::forApp(
            AppName::fromString($input->getArgument('app-name')),
            AppHostCollection::fromString($input->getArgument('app-hosts'))
        );

        $this->staticExporter->export($config);

        if (!$skipNetworking) {
            $this->networkingConfigurator->configure($config->appHosts());
        }

        if (!$skipDeploy) {
            $this->processRunner->mustRun(new Process('./bin/prod', $config->hostAppPath()));
        }
    }
}
