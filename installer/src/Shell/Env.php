<?php declare(strict_types=1);

namespace Cocotte\Shell;

interface Env
{
    public function put(string $name, string $value);

    public function get(string $name, $default = null): ?string;
}
