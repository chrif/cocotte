<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\DigitalOcean;

use Cocotte\DigitalOcean\Hostname;

final class HostnameActual
{
    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function fixture(): Hostname
    {
        return Hostname::parse(uniqid('hostname-').'.org');
    }

}