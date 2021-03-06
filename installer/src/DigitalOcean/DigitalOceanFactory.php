<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Assert\Assertion;
use DigitalOceanV2\Adapter\AdapterInterface;

final class DigitalOceanFactory
{

    public function adapter(string $adapterClass, ApiToken $token): AdapterInterface
    {
        Assertion::implementsInterface($adapterClass, AdapterInterface::class);

        return new $adapterClass($token->toString());
    }

}