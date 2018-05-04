<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Assert\Assertion;
use Cocotte\Environment\FromEnvLazyFactory;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Environment\LazyExportableOption;
use Cocotte\Shell\Env;

class MachineName implements LazyExportableOption, FromEnvLazyFactory
{
    const DEFAULT_VALUE = 'cocotte';
    const MACHINE_NAME = 'MACHINE_NAME';
    const OPTION_NAME = 'machine-name';
    /**
     * https://github.com/docker/machine/blob/v0.14.0/libmachine/host/host.go#L24
     */
    const REGEX = '/^[a-zA-Z0-9][a-zA-Z0-9\-\.]*$/';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value, "The machine name is empty");
        Assertion::regex($value, self::REGEX, "The machine name does not match ".self::REGEX);
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
        return new self($env->get(self::MACHINE_NAME, ""));
    }

    public static function toEnv(string $value, Env $env): void
    {
        $env->put(self::MACHINE_NAME, $value);
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