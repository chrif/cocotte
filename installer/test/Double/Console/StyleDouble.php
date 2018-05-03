<?php declare(strict_types=1);

namespace Cocotte\Test\Double\Console;

use Cocotte\Console\Style;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class StyleDouble
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

    public function outputSpy(): StyleOutputSpy
    {
        return new StyleOutputSpy();
    }

    /**
     * @return MockObject|Style
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(Style::class)->getMock();
    }

}