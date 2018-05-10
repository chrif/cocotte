<?php declare(strict_types=1);

namespace Cocotte\Help;

final class DefaultExamples implements CommandExamples
{
    /**
     * @param $token
     * @param $traefikHostname
     * @param $traefikPassword
     * @param $traefikUsername
     * @return string
     */
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

    /**
     * @param $token
     * @param $namespace
     * @param $hostname
     * @return string
     */
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

    /**
     * @param string $token
     * @param string $traefikHostname
     * @return string
     */
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
}