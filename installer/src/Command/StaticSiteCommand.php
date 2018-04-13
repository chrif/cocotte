<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\EnvironmentManager;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\StaticSite\StaticSiteExporter;
use Chrif\Cocotte\Template\StaticSite\StaticSiteHost;
use Chrif\Cocotte\Template\StaticSite\StaticSiteName;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class StaticSiteCommand extends Command
{
    /**
     * @var StaticSiteExporter
     */
    private $staticSiteExporter;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var EnvironmentManager
     */
    private $environmentManager;

    /**
     * @var MachineState
     */
    private $machineState;

    /**
     * @var StaticSiteHost
     */
    private $staticSiteHost;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(
        StaticSiteExporter $staticSiteExporter,
        NetworkingConfigurator $networkingConfigurator,
        EnvironmentManager $environmentManager,
        MachineState $machineState,
        StaticSiteHost $staticSiteHost,
        Style $style,
        ProcessRunner $processRunner
    ) {
        $this->staticSiteExporter = $staticSiteExporter;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->environmentManager = $environmentManager;
        $this->machineState = $machineState;
        $this->staticSiteHost = $staticSiteHost;
        $this->style = $style;
        $this->processRunner = $processRunner;
        parent::__construct();
    }

    public function isEnabled()
    {
        return $this->machineState->exists();
    }

    protected function configure()
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
            ->setDescription('Create a static website and deploy it to your Docker Machine.')
            ->getDefinition()->addOptions(
                [
                    StaticSiteName::inputOption(),
                    StaticSiteHost::inputOption(),
                    ApiToken::inputOption(),
                    MachineStoragePath::inputOption(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->environmentManager->exportFromInput($input);

        $skipNetworking = $input->getOption('skip-networking');
        $skipDeploy = $input->getOption('skip-deploy');

        if ($skipNetworking && !$skipDeploy) {
            throw new \Exception("Cannot skip networking when deploying");
        }

        $this->staticSiteExporter->export();

        if (!$skipNetworking) {
            $this->networkingConfigurator->configure($this->staticSiteHost->toHostCollection());
        }

        if (!$skipDeploy) {
            $this->style->title('Deploying exported site to cloud machine');
            $this->processRunner->mustRun(new Process('./bin/prod', $this->staticSiteExporter->hostAppPath()));
        }
    }
}
