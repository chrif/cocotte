<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

use Cocotte\Console\CommandInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CommandInterfaceDouble
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

    /**
     * @return MockObject|CommandInterface
     */
    public function optionProvidersMock(array $optionProviders): MockObject
    {
        ($mock = $this->mock())
            ->method('optionProviders')
            ->willReturn($optionProviders);

        return $mock;
    }

    /**
     * @return MockObject|CommandInterface
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(CommandInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

}