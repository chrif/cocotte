<?php

namespace Cocotte\Test\System\Command;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Command\StaticSiteCommandActual;

class StaticSiteCommandTest extends ApplicationTestCase
{
    /**
     * @group template
     */
    public function testExecute()
    {
        $this->assertCommandExecutes(
            StaticSiteCommandActual::get($this->container())->service(),
            [
                '--digital-ocean-api-token' => getenv('DIGITAL_OCEAN_API_TOKEN'),
                '--namespace' => getenv('STATIC_SITE_NAMESPACE'),
                '--hostname' => getenv('STATIC_SITE_HOSTNAME'),
            ]
        );
    }

}
