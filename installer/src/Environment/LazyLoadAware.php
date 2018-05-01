<?php declare(strict_types=1);

namespace Cocotte\Environment;

interface LazyLoadAware
{
    public function onLazyLoad(): void;
}