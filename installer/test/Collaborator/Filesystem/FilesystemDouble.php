<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Filesystem;

use Cocotte\Filesystem\CocotteFilesystem;
use Cocotte\Filesystem\Filesystem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class FilesystemDouble
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
     * @return MockObject|Filesystem
     */
    public function mock(): MockObject
    {
        return $this->testCase->getMockBuilder(Filesystem::class)->getMock();
    }

    /**
     * @return MockObject|Filesystem
     */
    public function expectNoCallToWriteMethods(): Filesystem
    {
        $mock = $this->testCase->getMockBuilder(CocotteFilesystem::class)
            ->setMethodsExcept([
                'isAbsolutePath',
                'exists',
                'readlink',
                'isLink',
            ])
            ->getMock();

        $mock->expects(TestCase::never())->method(TestCase::anything());

        return $mock;
    }
}