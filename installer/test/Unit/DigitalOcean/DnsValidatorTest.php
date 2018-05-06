<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\DigitalOcean;

use Cocotte\DigitalOcean\DnsValidator;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\Test\Collaborator\Shell\FakeEnv;
use Iodev\Whois\Whois;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DnsValidatorTest extends TestCase
{
    public function test_it_does_not_skip_validation_if_config_is_not_set()
    {
        /** @var MockObject|DnsValidator $validator */
        $validator = $this->getMockBuilder(DnsValidator::class)
            ->setMethodsExcept(['validateHost'])
            ->setConstructorArgs([$this->whoisMock(), $env = new FakeEnv()])
            ->getMock();

        $validator->expects(self::once())
            ->method('validateNameServers')
            ->willThrowException(new \Exception('not skipped'));

        self::assertFalse((bool)$env->get(DnsValidator::SKIP_DNS_VALIDATION));

        $this->expectExceptionMessage('not skipped');

        $validator->validateHost(Hostname::parse('example.com'));
    }

    public function test_it_skips_validation_if_config_is_trueish()
    {
        /** @var MockObject|DnsValidator $validator */
        $validator = $this->getMockBuilder(DnsValidator::class)
            ->setMethodsExcept(['validateHost'])
            ->setConstructorArgs([$this->whoisMock(), $env = new FakeEnv()])
            ->getMock();

        $validator->expects(self::never())
            ->method('validateNameServers');

        $env->put(DnsValidator::SKIP_DNS_VALIDATION, 'true');
        self::assertTrue((bool)$env->get(DnsValidator::SKIP_DNS_VALIDATION));

        $validator->validateHost(Hostname::parse('example.com'));
    }

    /**
     * @return MockObject|Whois
     * @throws \ReflectionException
     */
    private function whoisMock(): MockObject
    {
        return $this->createMock(Whois::class);
    }
}
