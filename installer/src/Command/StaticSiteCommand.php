<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenInteraction;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Environment\LazyEnvironmentLoader;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\StaticSite\StaticSiteCreator;
use Chrif\Cocotte\Template\StaticSite\StaticSiteHostname;
use Chrif\Cocotte\Template\StaticSite\StaticSiteNamespace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class StaticSiteCommand extends Command implements LazyEnvironment
{
    /**
     * @var StaticSiteCreator
     */
    private $staticSiteExporter;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var LazyEnvironmentLoader
     */
    private $lazyEnvironmentLoader;

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
     * @var ApiTokenInteraction
     */
    private $apiTokenInteraction;

    public function __construct(
        StaticSiteCreator $staticSiteExporter,
        NetworkingConfigurator $networkingConfigurator,
        LazyEnvironmentLoader $lazyEnvironmentLoader,
        StaticSiteHostname $staticSiteHostname,
        Style $style,
        ProcessRunner $processRunner,
        ApiTokenInteraction $apiTokenInteraction
    ) {
        $this->staticSiteExporter = $staticSiteExporter;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->lazyEnvironmentLoader = $lazyEnvironmentLoader;
        $this->staticSiteHostname = $staticSiteHostname;
        $this->style = $style;
        $this->processRunner = $processRunner;
        $this->apiTokenInteraction = $apiTokenInteraction;
        parent::__construct();
    }

    public function requires(): array
    {
        return [
            ApiToken::class,
            MachineName::class,
            MachineStoragePath::class,
        ];
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
                    StaticSiteNamespace::inputOption(),
                    StaticSiteHostname::inputOption(),
                    $this->apiTokenInteraction->option(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->lazyEnvironmentLoader->load($this, $input);
        $skipNetworking = $input->getOption('skip-networking');
        $skipDeploy = $input->getOption('skip-deploy');

        if ($skipNetworking && !$skipDeploy) {
            throw new \Exception("Cannot skip networking when deploying");
        }

        $this->staticSiteExporter->create();

        if (!$skipNetworking) {
            $this->networkingConfigurator->configure($this->staticSiteHostname->toHostnameCollection());
        }

        if (!$skipDeploy) {
            $this->style->title('Deploying exported site to cloud machine');
            $this->processRunner->mustRun(new Process('./bin/prod', $this->staticSiteExporter->hostAppPath()));
        }
    }
}
