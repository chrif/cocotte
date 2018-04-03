<?php

declare(strict_types=1);

namespace Chrif\Cocotte\Configuration\App;

use Assert\Assertion;
use DigitalOceanV2\Entity;

class AppHost
{

    const ROOT = '@';

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $subDomain;

    private function __construct(string $value)
    {
        $parts = explode('.', $value);
        Assertion::count($parts, 3);
        Assertion::allString($parts);
        $this->subDomain = $parts[0];
        $this->domain = "{$parts[1]}.{$parts[2]}";
        $this->value = $value;
    }

    public static function fromRegularSyntax(string $value): self
    {
        $parts = explode('.', $value);
        if (2 === count($parts)) {
            array_unshift($parts, self::ROOT);
        }

        return self::fromDigitalOceanSyntax(implode('.', $parts));
    }

    public static function fromDigitalOceanSyntax(string $value): self
    {
        return new self($value);
    }

    /**
     * @codeCoverageIgnore
     */
    public static function fixture(): self
    {
        return self::fromRegularSyntax(uniqid('host-').'.org');
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function subDomain(): string
    {
        return $this->subDomain;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function matchDomain(Entity\Domain $domain): bool
    {
        return $domain->name === $this->domain();
    }

    public function matchDomainRecord(Entity\DomainRecord $domainRecord): bool
    {
        return $domainRecord->name === $this->subDomain();
    }

    public function equals(AppHost $host): bool
    {
        return $this->value() === $host->value();
    }

    public function toRoot(): self
    {
        return new self(self::ROOT.".{$this->domain}");
    }

    public function isRoot(): bool
    {
        return self::ROOT === $this->subDomain;
    }
}