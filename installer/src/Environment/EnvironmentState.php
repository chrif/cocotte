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

class EnvironmentState
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
        try {
            $this->assertBare(ApiToken::DIGITAL_OCEAN_API_TOKEN);
            $this->assertBare(MachineName::MACHINE_NAME, MachineName::DEFAULT_VALUE);
            $this->assertBare(StaticSiteHostname::STATIC_SITE_HOSTNAME);
            $this->assertBare(StaticSiteNamespace::STATIC_SITE_NAMESPACE);
            $this->assertBare(TraefikHostname::TRAEFIK_UI_HOSTNAME);
            $this->assertBare(TraefikPassword::TRAEFIK_UI_PASSWORD);
            $this->assertBare(TraefikUsername::TRAEFIK_UI_USERNAME);
        } catch (NotBareException $e) {
            return false;
        }

        return true;
    }

    private function assertBare(string $name, $default = null)
    {
        $value = $this->defaultValue($name, $default);

        if ($value !== $default) {
            throw new NotBareException();
        }
    }
}
