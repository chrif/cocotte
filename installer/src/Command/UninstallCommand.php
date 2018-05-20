<?php declare(strict_types=1);

namespace Cocotte\Command;

use Cocotte\Console\AbstractCommand;
use Cocotte\Console\DocumentedCommand;
use Cocotte\Console\Style;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Cocotte\DigitalOcean\NetworkingConfigurator;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Help\DefaultExamples;
use Cocotte\Host\HostMountRequired;
use Cocotte\Machine\MachineName;
use Cocotte\Machine\MachineNameOptionProvider;
use Cocotte\Machine\MachineState;
use Cocotte\Machine\MachineStoragePath;
use Cocotte\Shell\ProcessRunner;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikHostnameOptionProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;

final class UninstallCommand extends AbstractCommand implements LazyEnvironment, HostMountRequired, DocumentedCommand
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
        $this->setName('uninstall')
            ->setDescription($this->description())
            ->setHelp(
                $this->formatHelp($this->description(),
                    (new DefaultExamples)->uninstall(),
                    (new DefaultExamples)->uninstallInteractive()
                )
            );
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->confirm();
        $this->networkingConfigurator->remove(
            $this->traefikHostname->toHostnameCollection()
        );
        if (!$this->machineState->exists()) {
            $this->style->verbose("Machine '{$this->machineName->toString()}' did not exist");
        } else {
            $this->processRunner->mustRun(new Process('docker-machine rm -f "${MACHINE_NAME}"'));
        }
        $this->style->complete("Machine is uninstalled and domain record is removed.");
    }

    private function confirm(): void
    {
        if (!$this->style->confirm(
            "You are about to uninstall a Docker machine named '<options=bold>{$this->machineName->toString()}</>' ".
            "on Digital Ocean \n and remove the domain record '<options=bold>{$this->traefikHostname->toString()}</>' ".
            "associated with \n this machine."
        )) {
            throw new \Exception('Cancelled');
        };
    }

    /**
     * @return string
     */
    private function description(): string
    {
        return 'Destroy the Docker machine on Digital Ocean and remove the Traefik subdomain.';
    }
}