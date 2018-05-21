<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

use Cocotte\Console\OptionProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class OptionProviderDouble
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
        $double = new self();
        $double->testCase = $testCase;

        return $double;
    }

    public function fake(string $name): OptionProviderFake
    {
        return new OptionProviderFake($name);
    }

    /**
     * @return MockObject|OptionProvider
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(OptionProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}