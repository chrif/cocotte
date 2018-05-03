<?php declare(strict_types=1);

namespace Cocotte\Environment;

use Cocotte\Shell\Env;

interface LazyLoadAware
{
    public function onLazyLoad(Env $env): void;
}