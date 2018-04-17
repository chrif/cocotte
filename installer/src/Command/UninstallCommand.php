<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenInteraction;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Environment\LazyEnvironmentLoader;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameInteraction;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameInteraction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class UninstallCommand extends Command implements LazyEnvironment
{
    /**
     * @var LazyEnvironmentLoader
     */
    private $lazyEnvironmentLoader;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

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
        LazyEnvironmentLoader $lazyEnvironmentLoader,
        ProcessRunner $processRunner,
        NetworkingConfigurator $networkingConfigurator,
        TraefikHostname $traefikHostname,
        Style $style,
        MachineName $machineName,
        TraefikHostnameInteraction $traefikHostnameInteraction,
        ApiTokenInteraction $apiTokenInteraction,
        MachineNameInteraction $machineNameInteraction
    ) {
        $this->lazyEnvironmentLoader = $lazyEnvironmentLoader;
        $this->processRunner = $processRunner;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->traefikHostname = $traefikHostname;
        $this->style = $style;
        $this->machineName = $machineName;
        $this->traefikHostnameInteraction = $traefikHostnameInteraction;
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
        ];
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
        $this->lazyEnvironmentLoader->load($this, $input);
        $this->confirm();
        $this->networkingConfigurator->configure($this->traefikHostname->toHostnameCollection(), true);
        $this->processRunner->mustRun(new Process('docker-machine rm -y "${MACHINE_NAME}"'));
    }

    private function confirm(): void
    {
        if (!$this->style->confirm(
            "You are about to uninstall a Docker Machine named '<options=bold>{$this->machineName->toString()}</>' ".
            "on Digital Ocean and remove the domain record '<options=bold>{$this->traefikHostname->toString()}</>' ".
            "associated with this machine.",
            false
        )) {
            throw new \Exception('Cancelled');
        };
    }
}