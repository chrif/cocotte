<?php

namespace Cocotte\Test\Unit\DigitalOcean;

use Assert\InvalidArgumentException;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Test\Double\Shell\FakeEnv;
use PHPUnit\Framework\TestCase;

class ApiTokenTest extends TestCase
{

    public function testOptionName()
    {
        self::assertSame('digital-ocean-api-token', ApiToken::optionName());
    }

    public function testFromEnv()
    {
        $env = new FakeEnv();
        $env->put('DIGITAL_OCEAN_API_TOKEN', 'foo');
        $token = ApiToken::fromEnv($env);
        self::assertSame('foo', $token->toString());
    }

    public function testToString()
    {
        $token = new ApiToken('foo');
        self::assertSame('foo', (string)$token);
    }

    public function testToEnv()
    {
        $env = new FakeEnv();
        ApiToken::toEnv('bar', $env);
        self::assertSame('bar', $env->get('DIGITAL_OCEAN_API_TOKEN'));
    }

    public function testNotEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The API token is empty.');
        new ApiToken('');
    }

}
