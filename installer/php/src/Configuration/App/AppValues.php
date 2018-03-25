<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\App;

use Chrif\Cocotte\CocotteConfiguration;
use Chrif\Cocotte\Configuration\ConfigurationValue;

class AppValues implements ConfigurationValue
{

    const APP = 'app';

    /**
     * @var AppName
     */
    private $name;

    /**
     * @var AppHostCollection
     */
    private $hosts;

    public function __construct(AppName $name, AppHostCollection $hosts)
    {
        $this->name = $name;
        $this->hosts = $hosts;
    }

    public static function fromArray(array $app): self
    {
        return new self(
            AppName::fromString($app[AppName::NAME]),
            AppHostCollection::fromString($app[AppHostCollection::HOSTS])
        );
    }

    public static function fromRoot(CocotteConfiguration $configuration): self
    {
        return self::fromArray($configuration->value()[self::APP]);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function fixture(): self
    {
        return new self(
            AppName::fixture(),
            AppHostCollection::fixture()
        );
    }

    public function name(): AppName
    {
        return $this->name;
    }

    /**
     * @return AppHostCollection|AppHost[]
     */
    public function hosts(): AppHostCollection
    {
        return $this->hosts;
    }
}