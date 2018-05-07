<?php declare(strict_types=1);

namespace Cocotte\Template\StaticSite;

use Assert\Assertion;
use Cocotte\Environment\FromEnvLazyFactory;
use Cocotte\Environment\LazyEnvironmentValue;
use Cocotte\Environment\LazyExportableOption;
use Cocotte\Shell\Env;

class StaticSiteNamespace implements LazyExportableOption, FromEnvLazyFactory
{
    const STATIC_SITE_NAMESPACE = 'STATIC_SITE_NAMESPACE';
    const OPTION_NAME = 'namespace';
    const REGEX = '/^[a-z0-9-]+$/';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value, "The site namespace is empty");
        Assertion::regex(
            $value,
            self::REGEX,
            "The site namespace does not contain only lowercase letters, digits and -"
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
        return new self($env->get(self::STATIC_SITE_NAMESPACE, ""));
    }

    public static function toEnv(string $value, Env $env): void
    {
        $env->put(self::STATIC_SITE_NAMESPACE, $value);
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