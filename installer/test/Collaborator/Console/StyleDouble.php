<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

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
        $double = new self();
        $double->testCase = $testCase;

        return $double;
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