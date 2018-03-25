<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration;

use Chrif\Cocotte\CocotteConfiguration;

class ApiToken implements ConfigurationValue
{

    const API_TOKEN = 'api_token';

    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromRoot(CocotteConfiguration $configuration): self
    {
        return new self($configuration->value()[self::API_TOKEN]);
    }

    public function value(): string
    {
        return $this->value;
    }

}