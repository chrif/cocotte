<?php declare(strict_types=1);

namespace Cocotte\Environment;

use Cocotte\Shell\Env;

interface FromEnvLazyFactory
{
    public static function fromEnv(Env $env): LazyEnvironmentValue;
}
