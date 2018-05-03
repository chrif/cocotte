<?php

namespace Cocotte\Test\System\Command;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Double\Command\NetworkingCommandMother;

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
            NetworkingCommandMother::get($this)->service($this->container()),
            [
                'hostnames' => $hostname,
                '--remove' => true,
            ]
        );
    }

}
