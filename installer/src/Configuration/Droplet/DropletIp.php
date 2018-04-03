<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\Droplet;

use Chrif\Cocotte\CocotteConfiguration;
use Chrif\Cocotte\Configuration\ConfigurationValue;

class DropletIp implements ConfigurationValue
{

    const IP = 'ip';

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
        return new self($configuration->value()[DropletValues::DROPLET][self::IP]);
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    public function equals(DropletIp $ip): bool
    {
        return $this->value() === $ip->value();
    }

}