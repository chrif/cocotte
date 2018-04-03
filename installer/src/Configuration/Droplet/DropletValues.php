<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\Droplet;

use Chrif\Cocotte\CocotteConfiguration;
use Chrif\Cocotte\Configuration\ConfigurationValue;

class DropletValues implements ConfigurationValue
{

    const DROPLET = 'droplet';

    /**
     * @var DropletName
     */
    private $name;

    /**
     * @var DropletIp
     */
    private $ip;

    /**
     * @param DropletName $name
     * @param DropletIp $ip
     */
    public function __construct(DropletName $name, DropletIp $ip)
    {
        $this->name = $name;
        $this->ip = $ip;
    }

    public static function fromDomain(DropletName $name, DropletIp $ip): self
    {
        return new self($name, $ip);
    }

    public static function fromArray(array $droplet): self
    {
        return new self(
            DropletName::fromString($droplet[DropletName::NAME]),
            DropletIp::fromString($droplet[DropletIp::IP])
        );
    }

    public static function fromRoot(CocotteConfiguration $configuration): self
    {
        return self::fromArray($configuration->value()[DropletValues::DROPLET]);
    }

    public function name(): DropletName
    {
        return $this->name;
    }

    public function ip(): DropletIp
    {
        return $this->ip;
    }

    public function toArray(): array
    {
        return [
            DropletName::NAME => $this->name()->value(),
            DropletIp::IP => $this->ip()->value(),
        ];
    }

}