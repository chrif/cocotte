<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenInteraction;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Environment\LazyEnvironmentLoader;
use Chrif\Cocotte\Machine\MachineCreator;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameInteraction;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikCreator;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameInteraction;
use Chrif\Cocotte\Template\Traefik\TraefikPassword;
use Chrif\Cocotte\Template\Traefik\TraefikPasswordInteraction;
use Chrif\Cocotte\Template\Traefik\TraefikUsername;
use Chrif\Cocotte\Template\Traefik\TraefikUsernameInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class InstallCommand extends Command implements LazyEnvironment
{
    /**
     * @var LazyEnvironmentLoader
     */
    private $lazyEnvironmentLoader;

    /**
     * @var MachineCreator
     */
    private $machineCreator;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var TraefikCreator
     */
    private $traefikCreator;

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
        LazyEnvironmentLoader $lazyEnvironmentLoader,
        MachineCreator $machineCreator,
        ProcessRunner $processRunner,
        TraefikCreator $traefikCreator,
        Style $style,
        NetworkingConfigurator $networkingConfigurator,
        MachineName $machineName,
        TraefikHostname $traefikHostname,
        TraefikPasswordInteraction $traefikPasswordInteraction,
        TraefikHostnameInteraction $traefikHostnameInteraction,
        TraefikUsernameInteraction $traefikUsernameInteraction,
        ApiTokenInteraction $apiTokenInteraction,
        MachineNameInteraction $machineNameInteraction
    ) {
        $this->lazyEnvironmentLoader = $lazyEnvironmentLoader;
        $this->machineCreator = $machineCreator;
        $this->processRunner = $processRunner;
        $this->traefikCreator = $traefikCreator;
        $this->style = $style;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->machineName = $machineName;
        $this->traefikHostname = $traefikHostname;
        $this->traefikPasswordInteraction = $traefikPasswordInteraction;
        $this->traefikHostnameInteraction = $traefikHostnameInteraction;
        $this->traefikUsernameInteraction = $traefikUsernameInteraction;
        $this->apiTokenInteraction = $apiTokenInteraction;
        $this->machineNameInteraction = $machineNameInteraction;
        parent::__construct();
    }

    public function requires(): array
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
        $this->lazyEnvironmentLoader->load($this, $input);
        $this->confirm();
        $this->machineCreator->create();
        $this->traefikCreator->create();
        $this->networkingConfigurator->configure($this->traefikHostname->toHostnameCollection());
        $this->style->title('Deploying exported site to cloud machine');
        $this->processRunner->mustRun(new Process('./bin/prod', $this->traefikCreator->hostAppPath()));
    }

    private function confirm(): void
    {
        if (!$this->style->confirm(
            "You are about to create a Docker Machine named '{$this->machineName->toString()}' on Digital Ocean ".
            "and install the Traefik reverse proxy on it with hostname '{$this->traefikHostname->toString()}'. ".
            "This action may take a few minutes."
        )) {
            throw new \Exception('Cancelled');
        };
    }

}