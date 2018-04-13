<?php

namespace Chrif\Cocotte\Environment;

interface ExportableValue
{
    public static function toEnv($value): void;
}