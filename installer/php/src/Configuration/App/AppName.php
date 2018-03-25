<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\CocotteConfiguration;
use Chrif\Cocotte\Configuration\ConfigurationValue;

class AppName implements ConfigurationValue
{

    const NAME = 'name';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromRoot(CocotteConfiguration $configuration): self
    {
        return self::fromString($configuration->value()[AppValues::APP][self::NAME]);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function fixture(): self
    {
        return new self(uniqid(self::class));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(AppName $name): bool
    {
        return $this->value() === $name->value();
    }
}