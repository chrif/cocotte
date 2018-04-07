<?php declare(strict_types=1);

namespace Chrif\Cocotte\Configuration;

use Chrif\Cocotte\Collection\GenericCollection;

final class AppHostCollection extends GenericCollection
{
    protected $values;

    public function __construct(AppHost ...$appHosts)
    {
        $this->values = $appHosts;
    }

    public static function fromScalarArray(array $value): self
    {
        return self::fromArray(
            array_map(
                function (string $host) {
                    return AppHost::parse($host);
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
        return new self(AppHost::fixture());
    }

    public function toString(): string
    {
        return implode(',', $this->values);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toLocal(): AppHostCollection
    {
        $localHosts = [];
        foreach ($this->values as $value) {
            $localHosts[] = $value->toLocal();
        }

        return self::fromArray($localHosts);
    }
}
