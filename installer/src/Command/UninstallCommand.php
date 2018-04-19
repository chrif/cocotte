<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\AbstractCommand;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameOptionProvider;
use Chrif\Cocotte\Machine\MachineState;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Shell\ProcessRunner;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameOptionProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

final class UninstallCommand extends AbstractCommand implements LazyEnvironment
{
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MachineState
     */
    private $machineState;

    public function __construct(
        ProcessRunner $processRunner,
        NetworkingConfigurator $networkingConfigurator,
        TraefikHostname $traefikHostname,
        Style $style,
        MachineName $machineName,
        EventDispatcherInterface $eventDispatcher,
        MachineState $machineState
    ) {
        $this->processRunner = $processRunner;
        $this->networkingConfigurator = $networkingConfigurator;
        $this->traefikHostname = $traefikHostname;
        $this->style = $style;
        $this->machineName = $machineName;
        $this->eventDispatcher = $eventDispatcher;
        $this->machineState = $machineState;
        parent::__construct();
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
            MachineName::class,
            MachineStoragePath::class,
            TraefikHostname::class,
        ];
    }

    public function optionProviders(): array
    {
        return [
            ApiTokenOptionProvider::class,
            MachineNameOptionProvider::class,
            TraefikHostnameOptionProvider::class,
        ];
    }

    protected function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function doConfigure(): void
    {
        $this
            ->setName('uninstall')
            ->setDescription('Destroy the Docker machine on Digital Ocean and remove the Traefik subdomain.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->confirm();
        $this->networkingConfigurator->configure($this->traefikHostname->toHostnameCollection(), true);
        if (!$this->machineState->exists()) {
            $this->style->verbose("Machine '{$this->machineName->toString()}' did not exist");
        } else {
            $this->processRunner->mustRun(new Process('docker-machine rm -f "${MACHINE_NAME}"'));
        }
        $this->style->complete("Machine is uninstalled and domain record(s) are removed.");
    }

    private function confirm(): void
    {
        if (!$this->style->confirm(
            "You are about to uninstall a Docker machine named '<options=bold>{$this->machineName->toString()}</>' ".
            "on Digital Ocean \nand remove the domain record(s) '<options=bold>{$this->traefikHostname->toString()}</>' ".
            "associated with \nthis machine."
        )) {
            throw new \Exception('Cancelled');
        };
    }
}