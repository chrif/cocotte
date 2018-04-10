<?php declare(strict_types=1);

namespace Chrif\Cocotte\Configuration;

use Assert\Assertion;
use DigitalOceanV2\Entity;

final class AppHost
{

    const DIGITAL_OCEAN_ROOT_RECORD = '@';
    const LOCAL_TOP_LEVEL_DOMAIN = 'local';

    /**
     * @var string
     */
    private $lowerLevelDomains;

    /**
     * @var string
     */
    private $secondLevelDomain;

    /**
     * @var string
     */
    private $topLevelDomain;

    public function __construct(string $lowerLevelDomains, string $secondLevelDomain, string $topLevelDomain)
    {
        $this->lowerLevelDomains = $lowerLevelDomains;
        $this->secondLevelDomain = $secondLevelDomain;
        $this->topLevelDomain = $topLevelDomain;
    }

    public static function fromString(string $value): self
    {
        $domains = explode('.', $value);
        Assertion::count($domains, 3);
        Assertion::allString($domains);
        list($lowerLevelDomains, $secondLevelDomain, $topLevelDomain) = $domains;

        return new self($lowerLevelDomains, $secondLevelDomain, $topLevelDomain);
    }

    public static function parse(string $value): self
    {
        $domains = explode('.', $value);

        if (count($domains) < 2) {
            throw new \Exception("'$value' does not have a first and second level domains");
        }
        if (count($domains) > 3) {
            throw new \Exception("'$value' is a domain with more than 3 levels.");
        }

        if (2 === count($domains)) {
            array_unshift($domains, self::DIGITAL_OCEAN_ROOT_RECORD);
        }

        return self::fromString(implode('.', $domains));
    }

    /**
     * @codeCoverageIgnore
     */
    public static function fixture(): self
    {
        return self::parse(uniqid('host-').'.org');
    }

    public function domainName(): string
    {
        return sprintf(
            "%s.%s",
            $this->secondLevelDomain,
            $this->topLevelDomain
        );
    }

    public function recordName(): string
    {
        return $this->lowerLevelDomains;
    }

    public function rawValue(): string
    {
        return sprintf(
            "%s.%s.%s",
            $this->lowerLevelDomains,
            $this->secondLevelDomain,
            $this->topLevelDomain
        );
    }

    public function toString(): string
    {
        if ($this->isRoot()) {
            return $this->domainName();
        } else {
            return $this->rawValue();
        }
    }

    public function matchDomainRecord(Entity\DomainRecord $domainRecord): bool
    {
        return $domainRecord->name === $this->recordName();
    }

    public function toRoot(): self
    {
        return self::parse($this->domainName());
    }

    public function isRoot(): bool
    {
        return self::DIGITAL_OCEAN_ROOT_RECORD === $this->lowerLevelDomains;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function isLocal()
    {
        return self::LOCAL_TOP_LEVEL_DOMAIN === $this->topLevelDomain;
    }

    public function toLocal(): AppHost
    {
        if ($this->isLocal()) {
            return $this;
        } else {
            return new self(
                $this->lowerLevelDomains,
                $this->secondLevelDomain,
                self::LOCAL_TOP_LEVEL_DOMAIN
            );
        }
    }

}