<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Chrif\Cocotte\Template\AppHostCollection;
use Symfony\Component\Console\Input\InputOption;

class TraefikUiHost implements ImportableValue, ExportableValue, InputOptionValue
{
    const TRAEFIK_UI_HOST = 'TRAEFIK_UI_HOST';
    const INPUT_OPTION = 'traefik-ui-host';

    /**
     * @var AppHostCollection
     */
    private $value;

    public function __construct(AppHostCollection $value)
    {
        $this->value = $value;
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
        return new self(AppHostCollection::fromString(Env::get(self::TRAEFIK_UI_HOST)));
    }

    public static function toEnv($value)
    {
        Env::put(self::TRAEFIK_UI_HOST, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Traefik Ui Host',
            Env::get(self::TRAEFIK_UI_HOST)
        );
    }

    public static function inputOptionName(): string
    {
        return self::INPUT_OPTION;
    }

    public function value(): AppHostCollection
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value()->toString();
    }

}