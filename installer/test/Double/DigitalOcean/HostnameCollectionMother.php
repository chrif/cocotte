<?php declare(strict_types=1);

namespace Cocotte\Test\Double\DigitalOcean;

use Cocotte\DigitalOcean\HostnameCollection;
use PHPUnit\Framework\TestCase;

final class HostnameCollectionMother
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

    public function fixture(): HostnameCollection
    {
        return new HostnameCollection(HostnameMother::create($this->testCase)->fixture());
    }

}