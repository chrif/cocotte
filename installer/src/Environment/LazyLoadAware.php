<?php declare(strict_types=1);

namespace Chrif\Cocotte\Environment;

interface LazyLoadAware
{
    public function onLazyLoad(): void;
}