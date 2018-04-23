<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Assert\Assertion;
use Cocotte\Collection\GenericCollection;

final class HostnameCollection extends GenericCollection
{
    protected $values;

    public function __construct(Hostname ...$hostnames)
    {
        Assertion::greaterThan($hostnames, 0, "There is no hostname");
        $this->values = $hostnames;
    }

    public static function fromScalarArray(array $value): self
    {
        return self::fromArray(
            array_map(
                function (string $host) {
                    return Hostname::parse($host);
                },
                $value
            )
        );
    }

    public static function fromString(string $string): self
    {
        return self::fromScalarArray(array_map('trim', explode(',', $string)));
    }

    /**
     * @codeCoverageIgnore
     */
    public static function fixture(): self
    {
        return new self(Hostname::fixture());
    }

    public function toString(): string
    {
        return implode(',', $this->values);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toLocal(): HostnameCollection
    {
        $localHosts = [];
        foreach ($this->values as $value) {
            $localHosts[] = $value->toLocal();
        }

        return self::fromArray($localHosts);
    }
}
