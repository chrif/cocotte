<?php

namespace Chrif\Cocotte\Environment;

use Symfony\Component\Console\Input\InputOption;

interface InputOptionValue
{
    public static function inputOptionName(): string;

    public static function inputOption(): InputOption;
}