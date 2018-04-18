<?php declare(strict_types=1);

namespace Chrif\Cocotte\Environment;

interface LazyEnvironmentValue
{
    const LAZY_ENVIRONMENT = 'lazy.environment';

    public static function fromEnv(): LazyEnvironmentValue;
}
