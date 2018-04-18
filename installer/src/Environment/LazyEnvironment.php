<?php

namespace Chrif\Cocotte\Environment;

interface LazyEnvironment
{
    /**
     * Class names for implementations of LazyEnvironmentValue
     *
     * @return string[]
     */
    public function lazyEnvironmentValues(): array;
}
