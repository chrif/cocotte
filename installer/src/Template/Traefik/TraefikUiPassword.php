<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class TraefikUiPassword implements ImportableValue, ExportableValue, InputOptionValue
{
    const TRAEFIK_UI_PASSWORD = 'TRAEFIK_UI_PASSWORD';
    const INPUT_OPTION = 'traefik-ui-password';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value, "The Traefik Ui password is empty");
        Assertion::regex($value, '/^[a-zA-Z0-9_-@#%?&*+=!]+$/', "The Traefik Ui password does not contain only alphanumeric characters and _-@#%?&*+=!");
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
        return new self(Env::get(self::TRAEFIK_UI_PASSWORD));
    }

    public static function toEnv($value): void
    {
        Env::put(self::TRAEFIK_UI_PASSWORD, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Traefik Ui password. Allowed characters are alphanumeric characters and _-@#%?&*+=!',
            Env::get(self::TRAEFIK_UI_PASSWORD)
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