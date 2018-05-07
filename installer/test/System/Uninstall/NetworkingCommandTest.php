<?php

namespace Cocotte\Test\System\Uninstall;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Command\NetworkingCommandActual;

class NetworkingCommandTest extends ApplicationTestCase
{
    public function testRemoveStaticSite()
    {
        $hostname = getenv('STATIC_SITE_HOSTNAME');

        if (getenv('KEEP_MACHINE_AFTER')) {
            self::markTestSkipped("$hostname not removed.");
        }

        $this->assertCommandExecutes(
            NetworkingCommandActual::get($this->container())->service(),
            [
                'hostnames' => $hostname,
                '--remove' => true,
            ]
        );
    }
}
