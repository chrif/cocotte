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
        /**
         * @see \Symfony\Component\Process\Process::getDefaultEnv
         */
        $_SERVER[$name] = $value;
    }

    public function get(string $name, $default = null): ?string
    {
        $value = getenv($name);
        if (false !== $value) {
            return $value;
        }

        return $default;
    }

    public function unset(string $name)
    {
        global $_SERVER;
        Assertion::true(putenv($name), "Could not unset env with name '$name'.");
        Assertion::same(
            null,
            $retrieved = self::get($name),
            "Failed asserting that env with name '$name' has been unset. ".
            "Retrieved value was '$retrieved'."
        );
        /**
         * @see \Symfony\Component\Process\Process::getDefaultEnv
         */
        unset($_SERVER[$name]);
        Assertion::keyNotExists(
            $_SERVER,
            $name,
            "Failed asserting that env with name '$name' has been unset in global _SERVER."
        );
    }

}
