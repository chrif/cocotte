<?php declare(strict_types=1);

namespace Chrif\Cocotte\Machine;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class MachineName implements ImportableValue, ExportableValue, InputOptionValue
{
    const COCOTTE_MACHINE = 'COCOTTE_MACHINE';
    const INPUT_OPTION = 'machine-name';

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
        return new self(Env::get(self::COCOTTE_MACHINE));
    }

    public static function toEnv($value)
    {
        Env::put(self::COCOTTE_MACHINE, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Machine Name',
            Env::get(self::COCOTTE_MACHINE)
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

    public function equals(MachineName $key): bool
    {
        return $this->value() === $key->value();
    }

    public function __toString()
    {
        return $this->value();
    }

}