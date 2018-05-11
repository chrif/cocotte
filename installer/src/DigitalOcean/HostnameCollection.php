<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class HostnameCollection implements IteratorAggregate, \Countable
{
    private $hostnames;

    public function __construct(Hostname ...$hostnames)
    {
        Assertion::greaterThan($hostnames, 0, "There is no hostname");
        $this->hostnames = $hostnames;
    }

    public static function fromScalarArray(array $value): self
    {
        return new self(
            ...array_map(
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

    public function toString(): string
    {
        return implode(',', $this->hostnames);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toLocal(): HostnameCollection
    {
        $localHosts = [];
        foreach ($this->hostnames as $value) {
            $localHosts[] = $value->toLocal();
        }

        return new self(...$localHosts);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->hostnames);
    }

    public function count()
    {
        return count($this->hostnames);
    }

}
