<?php

namespace Cocotte\Test\Unit\DigitalOcean;

use Assert\InvalidArgumentException;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Shell\Env;
use PHPUnit\Framework\TestCase;

class ApiTokenTest extends TestCase
{
    private $realToken;

    public function setUp()
    {
        $this->realToken = Env::get(ApiToken::DIGITAL_OCEAN_API_TOKEN);
    }

    public function tearDown()
    {
        Env::put(ApiToken::DIGITAL_OCEAN_API_TOKEN, $this->realToken);
    }

    public function testOptionName()
    {
        self::assertSame('digital-ocean-api-token', ApiToken::optionName());
    }

    public function testFromEnv()
    {
        Env::put('DIGITAL_OCEAN_API_TOKEN', 'foo');
        $token = ApiToken::fromEnv();
        self::assertSame('foo', $token->toString());
    }

    public function testToString()
    {
        $token = new ApiToken('foo');
        self::assertSame('foo', (string)$token);
    }

    public function testToEnv()
    {
        self::assertNotSame('bar', getenv('DIGITAL_OCEAN_API_TOKEN'));
        ApiToken::toEnv('bar');
        self::assertSame('bar', getenv('DIGITAL_OCEAN_API_TOKEN'));
    }

    public function testNotEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The API token is empty.');
        new ApiToken('');
    }

}
