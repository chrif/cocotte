<?php declare(strict_types=1);

namespace Cocotte\Template\Traefik;

use Assert\Assertion;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\DigitalOcean\HostnameCollection;
use Cocotte\Environment\FromEnvLazyFactory;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Environment\LazyExportableOption;
use Cocotte\Shell\Env;

class TraefikHostname implements LazyExportableOption, FromEnvLazyFactory
{
    const TRAEFIK_UI_HOSTNAME = 'TRAEFIK_UI_HOSTNAME';
    const OPTION_NAME = 'traefik-ui-hostname';
    const REGEX = '/^[a-zA-Z0-9_@#%?&*+=!-]+$/';

    /**
     * @var Hostname
     */
    private $hostname;

    public function __construct(Hostname $hostname)
    {
        Assertion::false($hostname->isRoot(), "$hostname does not have a subdomain.");
        $this->hostname = $hostname;
    }

    /**
     * @param Env $env
     * @return LazyEnvironmentValue|self
     * @throws \Exception
     */
    public static function fromEnv(Env $env): LazyEnvironmentValue
    {
        return new self(Hostname::parse($env->get(self::TRAEFIK_UI_HOSTNAME, "")));
    }

    public static function toEnv(string $value, Env $env): void
    {
        $env->put(self::TRAEFIK_UI_HOSTNAME, $value);
    }

    public static function optionName(): string
    {
        return self::OPTION_NAME;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->hostname->toString();
    }

    public function toHostnameCollection(): HostnameCollection
    {
        return new HostnameCollection($this->hostname);
    }

    public function toLocal(): Hostname
    {
        return $this->hostname->toLocal();
    }

    public function domainName()
    {
        return $this->hostname->domainName();
    }

    public function toHostname(): Hostname
    {
        return $this->hostname;
    }
}