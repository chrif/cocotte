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
    const HELP = 'Only alphanumeric characters and the specified special characters are allowed. '.self::REGEX;
    const REGEX = '/^[a-zA-Z0-9_@#%?&*+=!-]+$/';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value, "The Traefik Ui password is empty");
        Assertion::regex(
            $value,
            self::REGEX,
            "The Traefik Ui password does not contain only alphanumeric characters and _@#%?&*+=!-"
        );
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
            'Traefik Ui password. '.self::HELP,
            Env::get(self::TRAEFIK_UI_PASSWORD)
        );
    }

    public static function inputOptionName(): string
    {
        return self::INPUT_OPTION;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->toString();
    }
}