<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ExportableValue;
use Chrif\Cocotte\Environment\ImportableValue;
use Chrif\Cocotte\Environment\InputOptionValue;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;

class ApiToken implements ImportableValue, ExportableValue, InputOptionValue
{
    const DIGITAL_OCEAN_API_TOKEN = 'DIGITAL_OCEAN_API_TOKEN';
    const INPUT_OPTION = 'digital-ocean-api-token';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value);
        $this->value = $value;
    }

    public static function fromEnv(): ImportableValue
    {
        return new self(Env::get(self::DIGITAL_OCEAN_API_TOKEN));
    }

    public static function toEnv($value)
    {
        Env::put(self::DIGITAL_OCEAN_API_TOKEN, $value);
    }

    public static function inputOption(): InputOption
    {
        return new InputOption(
            self::INPUT_OPTION,
            null,
            InputOption::VALUE_REQUIRED,
            'Digital Ocean Api token',
            Env::get(self::DIGITAL_OCEAN_API_TOKEN)
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

}