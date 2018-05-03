<?php declare(strict_types=1);

namespace Cocotte\Shell;

use Assert\Assertion;

final class DefaultEnv implements Env
{

    public function put(string $name, string $value)
    {
        global $_SERVER;
        Assertion::true(putenv($name.'='.$value), "Could not put env with name '$name' and value '$value'.");
        Assertion::same(
            $value,
            $retrieved = self::get($name),
            "Failed asserting that value '$value' for env with name '$name' has been preserved when putting it. ".
            "Retrieved value was '$retrieved'."
        );
        $_SERVER[$name] = $value; // for \Symfony\Component\Process\Process::getDefaultEnv
    }

    public function get(string $name, $default = null): ?string
    {
        $value = getenv($name);
        if (false !== $value) {
            return $value;
        }

        return $default;
    }
}
