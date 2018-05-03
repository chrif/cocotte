<?php declare(strict_types=1);

namespace Cocotte\Template\StaticSite;

use Cocotte\DigitalOcean\Hostname;
use Cocotte\DigitalOcean\HostnameCollection;
use Cocotte\Environment\FromEnvLazyFactory;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Environment\LazyExportableOption;
use Cocotte\Shell\Env;

class StaticSiteHostname implements LazyExportableOption, FromEnvLazyFactory
{
    const STATIC_SITE_HOSTNAME = 'STATIC_SITE_HOSTNAME';
    const OPTION_NAME = 'hostname';

    /**
     * @var Hostname
     */
    private $hostname;

    public function __construct(Hostname $hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @param Env $env
     * @return LazyEnvironmentValue|self
     * @throws \Exception
     */
    public static function fromEnv(Env $env): LazyEnvironmentValue
    {
        return new self(Hostname::parse($env->get(self::STATIC_SITE_HOSTNAME, "")));
    }

    public static function toEnv(string $value, Env $env): void
    {
        $env->put(self::STATIC_SITE_HOSTNAME, $value);
    }

    public static function optionName(): string
    {
        return self::OPTION_NAME;
    }

    public function toLocal(): Hostname
    {
        return $this->hostname->toLocal();
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
        return HostnameCollection::fromArray([$this->hostname]);
    }
}