<?php declare(strict_types=1);

namespace Cocotte\Host;

use Assert\Assertion;
use Cocotte\Environment\LazyEnvironmentValue;

class HostMount implements LazyEnvironmentValue
{
    /**
     * @var array
     */
    private $value;

    public function __construct(array $value)
    {
        Assertion::keyExists($value, 'Source');
        Assertion::string($value['Source']);

        $this->value = $value;
    }

    /**
     * Absolute path on the host filesystem
     *
     * @return string
     */
    public function sourcePath(): string
    {
        return $this->value['Source'];
    }
}