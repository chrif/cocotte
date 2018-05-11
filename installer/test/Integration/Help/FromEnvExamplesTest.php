<?php declare(strict_types=1);

namespace Cocotte\Test\Integration\Help;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Template\StaticSite\StaticSiteHostname;
use Cocotte\Template\StaticSite\StaticSiteNamespace;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikUsername;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Help\FromEnvExamplesActual;

final class FromEnvExamplesTest extends ApplicationTestCase implements LazyEnvironment
{
    public function setUp()
    {
        $this->loadEnvironment();
    }

    public function testUninstall()
    {
        $actual = FromEnvExamplesActual::get($this->container())->service()->uninstall();

        $expected = <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte uninstall \
    --digital-ocean-api-token="\$DIGITAL_OCEAN_API_TOKEN" \
    --traefik-ui-hostname="\$TRAEFIK_UI_HOSTNAME";
EOF;

        self::assertEnvInString($expected, $actual);
    }

    public function testInstall()
    {
        $actual = FromEnvExamplesActual::get($this->container())->service()->install();

        $expected = <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte install \
    --digital-ocean-api-token="\$DIGITAL_OCEAN_API_TOKEN" \
    --traefik-ui-hostname="\$TRAEFIK_UI_HOSTNAME" \
    --traefik-ui-password="\$TRAEFIK_UI_PASSWORD" \
    --traefik-ui-username="\$TRAEFIK_UI_USERNAME";
EOF;

        self::assertEnvInString($expected, $actual);
    }

    public function lazyEnvironmentValues(): array
    {
        return [
            ApiToken::class,
            TraefikHostname::class,
            TraefikUsername::class,
            TraefikPassword::class,
            StaticSiteHostname::class,
            StaticSiteNamespace::class,
        ];
    }

}
