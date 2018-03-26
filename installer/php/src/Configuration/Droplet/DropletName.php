<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\Droplet;

use Chrif\Cocotte\CocotteConfiguration;
use Chrif\Cocotte\Configuration\ConfigurationValue;

class DropletName implements ConfigurationValue
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
        return new self($configuration->value()[DropletValues::DROPLET][self::NAME]);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(DropletName $key): bool
    {
        return $this->value() === $key->value();
    }

}