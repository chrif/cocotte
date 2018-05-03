<?php

namespace Cocotte\Test\System\Command;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Command\InstallCommandActual;

class InstallCommandTest extends ApplicationTestCase
{
    /**
     * @group install
     */
    public function testExecute()
    {
        $this->assertCommandExecutes(
            InstallCommandActual::get($this->container())->service(),
            [
                '--digital-ocean-api-token' => getenv('DIGITAL_OCEAN_API_TOKEN'),
                '--traefik-ui-hostname' => getenv('TRAEFIK_UI_HOSTNAME'),
                '--traefik-ui-password' => getenv('TRAEFIK_UI_PASSWORD'),
                '--traefik-ui-username' => getenv('TRAEFIK_UI_USERNAME'),
            ]
        );
    }

}
