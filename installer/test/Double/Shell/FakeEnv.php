<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Shell;

use Cocotte\Shell\Env;

final class FakeEnv implements Env
{
    private $env = array();

    public function put(string $name, string $value)
    {
        $this->env[$name] = $value;
    }

    public function get(string $name, $default = null): ?string
    {
        return $this->env[$name] ?? $default;
    }

}