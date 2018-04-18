<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\AbstractCommand;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Machine\MachineCreator;
use Chrif\Cocotte\Machine\MachineName;
use Chrif\Cocotte\Machine\MachineNameOptionProvider;
use Chrif\Cocotte\Machine\MachineStoragePath;
use Chrif\Cocotte\Template\Traefik\TraefikCreator;
use Chrif\Cocotte\Template\Traefik\TraefikHostname;
use Chrif\Cocotte\Template\Traefik\TraefikHostnameOptionProvider;
use Chrif\Cocotte\Template\Traefik\TraefikPassword;
use Chrif\Cocotte\Template\Traefik\TraefikPasswordOptionProvider;
use Chrif\Cocotte\Template\Traefik\TraefikUsername;
use Chrif\Cocotte\Template\Traefik\TraefikUsernameOptionProvider;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class InstallCommand extends AbstractCommand implements LazyEnvironment
{
    /**
     * @var MachineCreator
     */
    private $machineCreator;

    /**
     * @var TraefikCreator
     */
    private $traefikCreator;

    /**
     * @var Style
     */
    private $style;

    /**
     * @var MachineName
     */
    private $machineName;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        MachineCreator $machineCreator,
        TraefikCreator $traefikCreator,
        Style $style,
        MachineName $machineName,
        TraefikHostname $traefikHostname,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->machineCreator = $machineCreator;
        $this->traefikCreator = $traefikCreator;
        $this->style = $style;
        $this->machineName = $machineName;
        $this->traefikHostname = $traefikHostname;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }

    public function lazyEnvironmentValues(): array
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

    public function optionProviders(): array
    {
        return [
            ApiTokenOptionProvider::class,
            MachineNameOptionProvider::class,
            TraefikHostnameOptionProvider::class,
            TraefikPasswordOptionProvider::class,
            TraefikUsernameOptionProvider::class,
        ];
    }

    protected function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function doConfigure(): void
    {
        $this
            ->setName('install')
            ->setDescription('Create a <options=bold>Docker</> machine on <options=bold>Digital Ocean</> and install the <options=bold>Traefik</> reverse proxy on it.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->confirm();
        $this->machineCreator->create();
        $this->traefikCreator->create();
        $this->style->success("Installation successful. You can visit your Traefik UI at {$this->traefikHostname->toString()}");
    }

    private function confirm(): void
    {
        if (!$this->style->confirm(
            "You are about to create a Docker machine named '<options=bold>{$this->machineName->toString()}</>' on Digital Ocean ".
            "and install the Traefik reverse proxy on it with hostname(s) '<options=bold>{$this->traefikHostname->toString()}</>'. ".
            "This action may take a few minutes."
        )) {
            throw new \Exception('Cancelled');
        };
    }

}