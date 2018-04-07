<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Configuration\AppHostCollection;
use Chrif\Cocotte\Configuration\AppName;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\StaticTemplateConfiguration;
use Chrif\Cocotte\Template\StaticTemplateExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class StaticTemplateCommand extends Command
{
    /**
     * @var StaticTemplateExporter
     */
    private $staticTemplateExporter;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(
        StaticTemplateExporter $staticTemplateExporter,
        NetworkingConfigurator $networkingConfigurator,
        ProcessRunner $processRunner
    ) {
        $this->staticTemplateExporter = $staticTemplateExporter;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->processRunner = $processRunner;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cocotte:template:static')
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
            ->setDescription('Export static website template to host, configure networking, and deploy to prod.')
            ->setAliases(['est']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $skipNetworking = $input->getOption('skip-networking');
        $skipDeploy = $input->getOption('skip-deploy');

        if ($skipNetworking && !$skipDeploy) {
            throw new \Exception("Cannot skip networking when deploying");
        }

        $config = StaticTemplateConfiguration::forApp(
            AppName::fromString($input->getArgument('app-name')),
            AppHostCollection::fromString($input->getArgument('app-hosts'))
        );

        $this->staticTemplateExporter->export($config);

        if (!$skipNetworking) {
            $this->networkingConfigurator->configure($config->appHosts());
        }

        if (!$skipDeploy) {
            $this->processRunner->mustRun(new Process('./bin/prod', $config->hostAppPath()));
        }
    }
}
