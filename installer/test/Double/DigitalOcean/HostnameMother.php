<?php declare(strict_types=1);

namespace Cocotte\Test\Double\DigitalOcean;

use Cocotte\DigitalOcean\Hostname;
use PHPUnit\Framework\TestCase;

final class HostnameMother
{
    /**
     * @var TestCase
     */
    private $testCase;

    private function __construct()
    {
    }

    public static function create(TestCase $testCase): self
    {
        $mother = new self();
        $mother->testCase = $testCase;

        return $mother;
    }

    public function fixture(): Hostname
    {
        return Hostname::parse(uniqid('hostname-').'.org');
    }

}