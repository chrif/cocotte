<?php declare(strict_types=1);

namespace Cocotte\Help;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Template\StaticSite\StaticSiteHostname;
use Cocotte\Template\StaticSite\StaticSiteNamespace;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikUsername;

final class FromEnvExamples implements CommandExamples
{

    /**
     * @var ApiToken
     */
    private $apiToken;

    /**
     * @var TraefikHostname
     */
    private $traefikHostname;

    /**
     * @var TraefikUsername
     */
    private $traefikUsername;

    /**
     * @var TraefikPassword
     */
    private $traefikPassword;

    /**
     * @var StaticSiteHostname
     */
    private $staticSiteHostname;

    /**
     * @var StaticSiteNamespace
     */
    private $staticSiteNamespace;

    public function __construct(
        ApiToken $apiToken,
        TraefikHostname $traefikHostname,
        TraefikUsername $traefikUsername,
        TraefikPassword $traefikPassword,
        StaticSiteHostname $staticSiteHostname,
        StaticSiteNamespace $staticSiteNamespace
    ) {
        $this->apiToken = $apiToken;
        $this->traefikHostname = $traefikHostname;
        $this->traefikUsername = $traefikUsername;
        $this->traefikPassword = $traefikPassword;
        $this->staticSiteHostname = $staticSiteHostname;
        $this->staticSiteNamespace = $staticSiteNamespace;
    }

    public function install(): string
    {
        return (new DefaultExamples)->install(
            $this->apiToken->toString(),
            $this->traefikHostname->toString(),
            $this->traefikPassword->toString(),
            $this->traefikUsername->toString()
        );
    }

    public function staticSite(string $token = null, string $namespace = null, string $hostname = null): string
    {
        return (new DefaultExamples)->staticSite(
            $token ?? $this->apiToken->toString(),
            $namespace ?? $this->staticSiteNamespace->toString(),
            $hostname ?? $this->staticSiteHostname->toString()
        );
    }

    public function uninstall(): string
    {
        return (new DefaultExamples)->uninstall(
            $this->apiToken->toString(),
            $this->traefikHostname->toString()
        );
    }

}