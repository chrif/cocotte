<?php declare(strict_types=1);

namespace Cocotte\Help;

final class DefaultExamples implements CommandExamples
{
    public function install(
        string $token = 'xxxx',
        string $traefikHostname = 'traefik.mydomain.com',
        string $traefikPassword = 'password',
        string $traefikUsername = 'username'
    ): string {
        return <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte install \
    --digital-ocean-api-token="$token" \
    --traefik-ui-hostname="{$traefikHostname}" \
    --traefik-ui-password="{$traefikPassword}" \
    --traefik-ui-username="{$traefikUsername}";
EOF;
    }

    public function staticSite(
        string $token = 'xxxx',
        string $namespace = 'static-site',
        string $hostname = 'static-site.mydomain.com'
    ): string {
        return
            <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte static-site \
    --digital-ocean-api-token="{$token}" \
    --namespace="{$namespace}" \
    --hostname="{$hostname}";
EOF;
    }

    public function uninstall(
        string $token = 'xxxx',
        string $traefikHostname = 'traefik.mydomain.com'
    ): string {
        return <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte uninstall \
    --digital-ocean-api-token="{$token}" \
    --traefik-ui-hostname="{$traefikHostname}";
EOF;
    }

    public function installInteractive(): string
    {
        return <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte install;
EOF;
    }

    public function uninstallInteractive(): string
    {
        return <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte uninstall;
EOF;
    }

    public function staticSiteInteractive(): string
    {
        return <<<EOF
docker run -it --rm \
    -v "$(pwd)":/host \
    -v /var/run/docker.sock:/var/run/docker.sock:ro \
    chrif/cocotte static-site;
EOF;
    }

}
