<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\StaticSite;

use Chrif\Cocotte\DigitalOcean\AppHostCollection;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class StaticSiteHost implements ImportableValue, ExportableValue, InputOptionValue
{
    const STATIC_SITE_HOST = 'STATIC_SITE_HOST';
    const INPUT_OPTION = 'hostname';

    /**
     * @var AppHostCollection
     */
    private $appHostCollection;

    public function __construct(AppHostCollection $appHostCollection)
    {
        $this->appHostCollection = $appHostCollection;
    }

    public static function fromString(string $value): self
    {
        return new self(AppHostCollection::fromString($value));
    }

    /**
     * @return ImportableValue|self
     */
    public static function fromEnv(): ImportableValue
    {
        return new self(AppHostCollection::fromString(Env::get(self::STATIC_SITE_HOST)));
    }

    public static function toEnv($value)
    {
        Env::put(self::STATIC_SITE_HOST, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Comma-separated list of host(s) for the deployed website.',
            Env::get(self::STATIC_SITE_HOST)
        );
    }

    public static function inputOptionName(): string
    {
        return self::INPUT_OPTION;
    }

    public function toLocalHostCollection(): AppHostCollection
    {
        return $this->appHostCollection->toLocal();
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        return $this->appHostCollection->toString();
    }

    public function toHostCollection(): AppHostCollection
    {
        return $this->appHostCollection;
    }

}