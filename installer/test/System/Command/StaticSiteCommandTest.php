<?php

namespace Cocotte\Test\System\Command;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Double\Command\StaticSiteCommandMother;

class StaticSiteCommandTest extends ApplicationTestCase
{
    /**
     * @group template
     */
    public function testExecute()
    {
        $this->assertCommandExecutes(
            StaticSiteCommandMother::get($this)->service($this->container()),
            [
                '--digital-ocean-api-token' => getenv('DIGITAL_OCEAN_API_TOKEN'),
                '--namespace' => getenv('STATIC_SITE_NAMESPACE'),
                '--hostname' => getenv('STATIC_SITE_HOSTNAME'),
            ]
        );
    }

}
