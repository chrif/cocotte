<?php

namespace Chrif\Cocotte\Environment;

interface ImportableValue
{
    public static function fromEnv(): ImportableValue;
}