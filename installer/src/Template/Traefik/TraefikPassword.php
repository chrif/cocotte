<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Assert\Assertion;
use Chrif\Cocotte\Environment\LazyEnvironmentValue;
use Chrif\Cocotte\Environment\LazyExportableOption;
use Chrif\Cocotte\Shell\Env;

class TraefikPassword implements LazyExportableOption
{
    const TRAEFIK_UI_PASSWORD = 'TRAEFIK_UI_PASSWORD';
    const OPTION_NAME = 'traefik-ui-password';
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
     * @return LazyEnvironmentValue|self
     */
    public static function fromEnv(): LazyEnvironmentValue
    {
        return new self(Env::get(self::TRAEFIK_UI_PASSWORD));
    }

    public static function toEnv(string $value): void
    {
        Env::put(self::TRAEFIK_UI_PASSWORD, $value);
    }

    public static function optionName(): string
    {
        return self::OPTION_NAME;
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