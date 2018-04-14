<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\StaticSite;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class StaticSiteNamespace implements ImportableValue, ExportableValue, InputOptionValue
{
    const STATIC_SITE_NAMESPACE = 'STATIC_SITE_NAMESPACE';
    const INPUT_OPTION = 'namespace';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value, "The site namespace is empty");
        Assertion::regex($value, '/^[a-z0-9-]+$/', "The site namespace does not contain only lowercase letters, digits and -");
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
        return new self(Env::get(self::STATIC_SITE_NAMESPACE));
    }

    public static function toEnv($value): void
    {
        Env::put(self::STATIC_SITE_NAMESPACE, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Namespace for the site. Allowed characters are lowercase letters, digits and -',
            Env::get(self::STATIC_SITE_NAMESPACE)
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