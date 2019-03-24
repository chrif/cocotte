<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Assert\Assertion;
use Cocotte\Environment\FromEnvLazyFactory;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Environment\LazyExportableOption;
use Cocotte\Shell\Env;

class ApiToken implements LazyExportableOption, FromEnvLazyFactory
{
    const DIGITAL_OCEAN_API_TOKEN = 'DIGITAL_OCEAN_API_TOKEN';
    const OPTION_NAME = 'digital-ocean-api-token';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value, "The API token is empty.");
        $this->value = $value;
    }

    /**
     * @param Env $env
     * @return LazyEnvironmentValue|ApiToken
     */
    public static function fromEnv(Env $env): LazyEnvironmentValue
    {
        return new self($env->get(self::DIGITAL_OCEAN_API_TOKEN, ""));
    }

    public static function toEnv(string $value, Env $env): void
    {
        $env->put(self::DIGITAL_OCEAN_API_TOKEN, $value);
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
