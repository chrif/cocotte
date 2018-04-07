<?php declare(strict_types=1);

namespace Chrif\Cocotte\Configuration;

use Assert\Assertion;

final class MachineName implements EnvironmentValue
{
    const COCOTTE_MACHINE = 'COCOTTE_MACHINE';

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

    public static function fromEnv()
    {
        return new self(getenv(self::COCOTTE_MACHINE));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(MachineName $key): bool
    {
        return $this->value() === $key->value();
    }

}