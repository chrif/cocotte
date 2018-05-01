<?php declare(strict_types=1);

namespace Cocotte\Host;

final class HostException extends \Exception
{
    public static function noHostMount()
    {
        return new self(
            "There is no writable bind mount with the destination '/host'.\nMake sure you run your command ".
            "with a volume like this:\n-v \"$(pwd)\":/host"
        );
    }

    public static function noSocketMount(string $message)
    {
        return new self(
            $message."\nMake sure you start Docker and run your command ".
            "with a volume like this:\n-v /var/run/docker.sock:/var/run/docker.sock:ro"
        );
    }

}