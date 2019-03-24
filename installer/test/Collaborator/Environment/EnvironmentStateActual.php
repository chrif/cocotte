<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Environment;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Environment\EnvironmentState;
use Cocotte\Machine\MachineName;
use Cocotte\Template\StaticSite\StaticSiteHostname;
use Cocotte\Template\StaticSite\StaticSiteNamespace;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikUsername;
use Cocotte\Test\Collaborator\Shell\EnvActual;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class EnvironmentStateActual
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private function __construct()
    {
    }

    public static function get(ContainerInterface $container): self
    {
        $actual = new self();
        $actual->container = $container;

        return $actual;
    }

    public function service(): EnvironmentState
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get(EnvironmentState::class);
    }

    /**
     */
    public function makeBare()
    {
        $env = EnvActual::get($this->container)->service();

        TestCase::assertFalse($this->service()->isBare());

        $env->put(MachineName::MACHINE_NAME, MachineName::DEFAULT_VALUE);
        $env->unset(ApiToken::DIGITAL_OCEAN_API_TOKEN);
        $env->unset(StaticSiteHostname::STATIC_SITE_HOSTNAME);
        $env->unset(StaticSiteNamespace::STATIC_SITE_NAMESPACE);
        $env->unset(TraefikHostname::TRAEFIK_UI_HOSTNAME);
        $env->unset(TraefikPassword::TRAEFIK_UI_PASSWORD);
        $env->unset(TraefikUsername::TRAEFIK_UI_USERNAME);

        TestCase::assertTrue($this->service()->isBare());
    }
}
