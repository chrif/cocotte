<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\StaticSite;

use Assert\Assertion;
use Chrif\Cocotte\Environment\LazyEnvironmentValue;
use Chrif\Cocotte\Environment\LazyExportableOption;
use Chrif\Cocotte\Shell\Env;

class StaticSiteNamespace implements LazyExportableOption
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
     * @return LazyEnvironmentValue|self
     */
    public static function fromEnv(): LazyEnvironmentValue
    {
        return new self(Env::get(self::STATIC_SITE_NAMESPACE));
    }

    public static function toEnv(string $value): void
    {
        Env::put(self::STATIC_SITE_NAMESPACE, $value);
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