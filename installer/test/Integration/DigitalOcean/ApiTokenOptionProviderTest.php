<?php declare(strict_types=1);

namespace Cocotte\Test\Integration\DigitalOcean;

use Cocotte\DigitalOcean\ApiTokenOptionProvider;
use Cocotte\Test\Collaborator\Console\StyleDouble;
use PHPUnit\Framework\TestCase;

final class ApiTokenOptionProviderTest extends TestCase
{

    public function testValidate()
    {
        $provider = new ApiTokenOptionProvider(StyleDouble::create($this)->mock());
        $this->expectExceptionMessage(
            "Failed to validate that the Digital Ocean token has 'write' permissions. ".
            "Error message was:\nUnable to authenticate you."
        );
        $provider->validate('foobar');
    }
}
