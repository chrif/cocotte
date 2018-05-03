<?php declare(strict_types=1);

namespace Cocotte\Host;

use Cocotte\Environment\LazyEnvironmentValue;

class Mounts implements LazyEnvironmentValue
{
    /**
     * @var array
     */
    private $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }

}