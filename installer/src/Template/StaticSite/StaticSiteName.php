<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\StaticSite;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class StaticSiteName implements ImportableValue, ExportableValue, InputOptionValue
{
    const STATIC_SITE_NAME = 'STATIC_SITE_NAME';
    const INPUT_OPTION = 'app-name';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value);
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
        return new self(Env::get(self::STATIC_SITE_NAME));
    }

    public static function toEnv($value)
    {
        Env::put(self::STATIC_SITE_NAME, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Name for the exported website. Must be a valid directory name on the host.',
            Env::get(self::STATIC_SITE_NAME)
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