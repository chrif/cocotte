<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\DigitalOcean;

use Cocotte\Shell\Env;
use Cocotte\Test\Collaborator\Shell\FakeEnv;
use Iodev\Whois\Whois;
use PHPUnit\Framework\TestCase;

final class DnsValidatorDoubleBuilder
{
    /**
     * @var Whois
     */
    private $whois;
    /**
     * @var Env
     */
    private $env;

    public function __construct(TestCase $testCase)
    {
        $this->whois = DnsValidatorDouble::create($testCase)->whoisMock();
        $this->env = new FakeEnv();
    }

    /**
     * @return Whois
     */
    public function whois(): Whois
    {
        return $this->whois;
    }

    /**
     * @param Whois $whois
     * @return DnsValidatorDoubleBuilder
     */
    public function setWhois(Whois $whois): DnsValidatorDoubleBuilder
    {
        $this->whois = $whois;

        return $this;
    }

    /**
     * @return Env
     */
    public function env(): Env
    {
        return $this->env;
    }

    /**
     * @param Env $env
     * @return DnsValidatorDoubleBuilder
     */
    public function setEnv(Env $env): DnsValidatorDoubleBuilder
    {
        $this->env = $env;

        return $this;
    }

    public function args(): array
    {
        return [
            $this->whois,
            $this->env,
        ];
    }

}