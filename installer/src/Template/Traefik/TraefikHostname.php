<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\DigitalOcean\HostnameCollection;
use Chrif\Cocotte\Environment\LazyEnvironmentValue;
use Chrif\Cocotte\Environment\LazyExportableOption;
use Chrif\Cocotte\Shell\Env;

class TraefikHostname implements LazyExportableOption
{
    const TRAEFIK_UI_HOSTNAME = 'TRAEFIK_UI_HOSTNAME';
    const OPTION_NAME = 'traefik-ui-hostname';
    const REGEX = '/^[a-zA-Z0-9_@#%?&*+=!-]+$/';
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
        return new self(HostnameCollection::fromString(Env::get(self::TRAEFIK_UI_HOSTNAME)));
    }

    public static function toEnv(string $value): void
    {
        Env::put(self::TRAEFIK_UI_HOSTNAME, $value);
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

}