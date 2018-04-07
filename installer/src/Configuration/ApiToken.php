<?php declare(strict_types=1);

namespace Chrif\Cocotte\Configuration;

use Assert\Assertion;

final class ApiToken implements EnvironmentValue
{
    const DIGITAL_OCEAN_API_TOKEN = 'DIGITAL_OCEAN_API_TOKEN';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        Assertion::notEmpty($value);
        $this->value = $value;
    }

    public static function fromEnv()
    {
        return new self(getenv(self::DIGITAL_OCEAN_API_TOKEN));
    }

    public function value(): string
    {
        return $this->value;
    }

}