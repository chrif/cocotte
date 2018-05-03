<?php

namespace Cocotte\Test\System\Command;

use Cocotte\Test\Actual\ApplicationTestCase;
use Cocotte\Test\Actual\Command\NetworkingCommandActual;

class NetworkingCommandTest extends ApplicationTestCase
{
    /**
     * @group uninstall
     */
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
