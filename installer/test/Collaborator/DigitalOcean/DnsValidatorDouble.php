<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\DigitalOcean;

use Cocotte\DigitalOcean\DnsValidator;
use Iodev\Whois\Whois;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DnsValidatorDouble
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
     * @param \Closure $closure
     * @return DnsValidator|MockObject
     */
    public function buildMock(\Closure $closure): MockObject
    {
        $builder = $this->testCase->getMockBuilder(DnsValidator::class);
        $closure($builder);

        return $builder->getMock();
    }

    /**
     * @return MockObject|Whois
     */
    public function whoisMock(): MockObject
    {
        return $this->testCase->getMockBuilder(Whois::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function builder(): DnsValidatorDoubleBuilder
    {
        return new DnsValidatorDoubleBuilder($this->testCase);
    }
}