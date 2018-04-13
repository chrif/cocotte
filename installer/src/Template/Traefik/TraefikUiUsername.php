<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class TraefikUiUsername implements ImportableValue, ExportableValue, InputOptionValue
{
    const TRAEFIK_UI_USERNAME = 'TRAEFIK_UI_USERNAME';
    const INPUT_OPTION = 'traefik-ui-username';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value, "The Traefik Ui username is empty");
        Assertion::regex($value, '/^[a-zA-Z0-9]+$/', "The Traefik Ui username does not contain only alphanumeric characters.");
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * @return ImportableValue|self
     */
    public static function fromEnv(): ImportableValue
    {
        return new self(Env::get(self::TRAEFIK_UI_USERNAME));
    }

    public static function toEnv($value): void
    {
        Env::put(self::TRAEFIK_UI_USERNAME, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Traefik Ui username.  Allowed characters are alphanumeric characters.',
            Env::get(self::TRAEFIK_UI_USERNAME)
        );
    }

    public static function inputOptionName(): string
    {
        return self::INPUT_OPTION;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->value();
    }
}