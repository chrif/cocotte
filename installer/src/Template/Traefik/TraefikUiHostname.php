<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\DigitalOcean\HostnameCollection;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class TraefikUiHostname implements ImportableValue, ExportableValue, InputOptionValue
{
    const TRAEFIK_UI_HOSTNAME = 'TRAEFIK_UI_HOSTNAME';
    const INPUT_OPTION = 'traefik-ui-hostname';

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
     * @return ImportableValue|self
     */
    public static function fromEnv(): ImportableValue
    {
        return new self(HostnameCollection::fromString(Env::get(self::TRAEFIK_UI_HOSTNAME)));
    }

    public static function toEnv($value): void
    {
        Env::put(self::TRAEFIK_UI_HOSTNAME, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Traefik Ui hostname',
            Env::get(self::TRAEFIK_UI_HOSTNAME)
        );
    }

    public static function inputOptionName(): string
    {
        return self::INPUT_OPTION;
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