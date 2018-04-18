<?php declare(strict_types=1);

namespace Chrif\Cocotte\Environment;

interface LazyExportableOption extends LazyEnvironmentValue
{
    public static function optionName(): string;

    public static function toEnv(string $value): void;
}
