<?php

namespace Cocotte\Test\System\Command;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Command\UninstallCommandActual;

class UnInstallCommandTest extends ApplicationTestCase
{
    /**
     * @group uninstall
     */
    public function testExecute()
    {
        if (getenv('KEEP_MACHINE_AFTER')) {
            self::markTestSkipped("Machine not removed.");
        }

        $this->assertCommandExecutes(
            UninstallCommandActual::get($this->container())->service(),
            [
                '--digital-ocean-api-token' => getenv('DIGITAL_OCEAN_API_TOKEN'),
                '--traefik-ui-hostname' => getenv('TRAEFIK_UI_HOSTNAME'),
            ]
        );
    }

}
