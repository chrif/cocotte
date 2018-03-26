<?php

namespace Chrif\Cocotte\Configuration;

use Chrif\Cocotte\CocotteConfiguration;

interface ConfigurationValue
{

    public static function fromRoot(CocotteConfiguration $configuration);

}