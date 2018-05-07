<?php declare(strict_types=1);

namespace Cocotte\Environment;

use Cocotte\Shell\Env;

interface LazyExportableOption extends LazyEnvironmentValue
{
    public static function optionName(): string;

    public static function toEnv(string $value, Env $env): void;
}
