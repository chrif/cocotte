<?php declare(strict_types=1);

namespace Chrif\Cocotte\Environment;

interface LazyEnvironmentValue
{
    public static function fromEnv(): LazyEnvironmentValue;
}
