<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\CocotteConfiguration;
use Chrif\Cocotte\Configuration\ConfigurationValue;
use Chrif\Cocotte\Util\GenericCollection;

class AppHostCollection extends GenericCollection implements ConfigurationValue
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

    public static function fromRoot(CocotteConfiguration $configuration): self
    {
        return self::fromString($configuration->value()[AppValues::APP][AppHostCollection::HOSTS]);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function fixture(): self
    {
        return new self(AppHost::fixture());
    }
}
