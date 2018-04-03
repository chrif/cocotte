<?php

declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Assert\Assertion;
use Chrif\Cocotte\Configuration\ApiToken;
use DigitalOceanV2\Adapter\AdapterInterface;

class DigitalOceanFactory
{

    public function adapter(string $adapterClass, ApiToken $token): AdapterInterface
    {
        Assertion::implementsInterface($adapterClass, AdapterInterface::class);

        return new $adapterClass($token->value());
    }

}