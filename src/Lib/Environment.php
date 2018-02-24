<?php

declare(strict_types=1);


namespace App\Lib;


class Environment
{
    public static function get(): Environment
    {
        return new self();
    }

    public function machineName(): string
    {
        return $this->value('MACHINE_NAME');
    }

    public function digitalOceanToken(): string
    {
        return $this->value('DIGITAL_OCEAN_API_TOKEN');
    }

    private function value(string $key): string
    {
        $value = getenv($key);
        if (!$value) {
            throw new \Exception("There is no $key environment variable");
        }

        return $value;
    }
}