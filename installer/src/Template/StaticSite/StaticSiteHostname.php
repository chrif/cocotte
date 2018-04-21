<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\StaticSite;

use Chrif\Cocotte\DigitalOcean\HostnameCollection;
use Chrif\Cocotte\Environment\LazyEnvironmentValue;
use Chrif\Cocotte\Environment\LazyExportableOption;
use Chrif\Cocotte\Shell\Env;

class StaticSiteHostname implements LazyExportableOption
{
    const STATIC_SITE_HOSTNAME = 'STATIC_SITE_HOSTNAME';
    const OPTION_NAME = 'hostname';

    /**
     * @var HostnameCollection
     */
    private $hostnameCollection;

    public function __construct(HostnameCollection $hostnameCollection)
    {
        $this->hostnameCollection = $hostnameCollection;
    }

    public static function fromString(string $value): self
    {
        return new self(HostnameCollection::fromString($value));
    }

    /**
     * @return LazyEnvironmentValue|self
     */
    public static function fromEnv(): LazyEnvironmentValue
    {
        return new self(HostnameCollection::fromString(Env::get(self::STATIC_SITE_HOSTNAME, "")));
    }

    public static function toEnv(string $value): void
    {
        Env::put(self::STATIC_SITE_HOSTNAME, $value);
    }

    public static function optionName(): string
    {
        return self::OPTION_NAME;
    }

    public function toLocalHostnameCollection(): HostnameCollection
    {
        return $this->hostnameCollection->toLocal();
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        return $this->hostnameCollection->toString();
    }

    public function toHostnameCollection(): HostnameCollection
    {
        return $this->hostnameCollection;
    }

    public function formatSecureUrl(): string
    {
        return $this->hostnameCollection->formatSecureUrl();
    }
}