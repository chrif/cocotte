<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\DigitalOcean;

use Cocotte\DigitalOcean\ApiToken;

final class ApiTokenFixture
{
    public static function fixture(): ApiToken
    {
        return new ApiToken(uniqid());
    }
}