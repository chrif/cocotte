<?php declare(strict_types=1);

namespace Chrif\Cocotte\Shell;

use Assert\Assertion;

final class Env
{

    public static function put(string $name, string $value)
    {
        Assertion::true(putenv($name.'='.$value), "Could not put env with name '$name' and value '$value'.");
        Assertion::same(
            $value,
            $retrieved = self::get($name),
            "Failed asserting that value '$value' for env with name '$name' has been preserved when putting it. ".
            "Retrieved value was '$retrieved'."
        );
    }

    public static function get(string $name): ?string
    {
        $value = getenv($name);
        if (false !== $value) {
            return $value;
        }

        return null;
    }
}
