<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\Util\GenericCollection;

class AppHostCollection extends GenericCollection
{

    const HOSTS = 'hosts';

    protected $values;

    public function __construct(AppHost ...$domains)
    {
        $this->values = $domains;
    }

    public static function fromScalarArray(array $value): self
    {
        return self::fromArray(
            array_map(
                function (string $host) {
                    return AppHost::fromRegularSyntax($host);
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
}
