<?php declare(strict_types=1);

namespace Cocotte\Environment;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Machine\MachineName;
use Cocotte\Shell\Env;
use Cocotte\Template\StaticSite\StaticSiteHostname;
use Cocotte\Template\StaticSite\StaticSiteNamespace;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikUsername;

final class EnvironmentState
{
    /**
     * @var Env
     */
    private $env;

    public function __construct(Env $env)
    {
        $this->env = $env;
    }

    public function defaultValue(string $name, $default = null): ?string
    {
        return $this->env->get($name, $default);
    }

    public function isBare(): bool
    {
        //@formatter:off
        return
            $this->hasBareValue(ApiToken::DIGITAL_OCEAN_API_TOKEN) &&
            $this->hasBareValue(MachineName::MACHINE_NAME, MachineName::DEFAULT_VALUE) &&
            $this->hasBareValue(StaticSiteHostname::STATIC_SITE_HOSTNAME) &&
            $this->hasBareValue(StaticSiteNamespace::STATIC_SITE_NAMESPACE) &&
            $this->hasBareValue(TraefikHostname::TRAEFIK_UI_HOSTNAME) &&
            $this->hasBareValue(TraefikPassword::TRAEFIK_UI_PASSWORD) &&
            $this->hasBareValue(TraefikUsername::TRAEFIK_UI_USERNAME);
        //@formatter:on
    }

    private function hasBareValue(string $name, $default = null): bool
    {
        $value = $this->defaultValue($name, $default);

        return $value === $default;
    }
}
