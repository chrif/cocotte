<?php declare(strict_types=1);

namespace Cocotte\Template\Traefik;

use Assert\Assertion;
use Cocotte\Environment\FromEnvLazyFactory;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Environment\LazyExportableOption;
use Cocotte\Shell\Env;

class TraefikPassword implements LazyExportableOption, FromEnvLazyFactory
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
     * @param Env $env
     * @return LazyEnvironmentValue|self
     */
    public static function fromEnv(Env $env): LazyEnvironmentValue
    {
        return new self($env->get(self::TRAEFIK_UI_PASSWORD, ""));
    }

    public static function toEnv(string $value, Env $env): void
    {
        $env->put(self::TRAEFIK_UI_PASSWORD, $value);
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