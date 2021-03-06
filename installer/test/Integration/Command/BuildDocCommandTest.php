<?php

namespace Cocotte\Test\Integration\Command;

use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Command\BuildDocCommandActual;
use Cocotte\Test\Collaborator\Environment\EnvironmentStateActual;
use PHPUnit\Framework\TestCase;

class BuildDocCommandTest extends ApplicationTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function test_doc_is_up_to_date()
    {
        self::markTestSkipped('broken test');

        EnvironmentStateActual::get($this->container())->makeBare();

        $buildDocCommand = BuildDocCommandActual::get($this->container())->service();
        $buildDocCommand->setApplication($this->application()->console());

        $tester = $this->assertCommandExecutes($buildDocCommand, [], false);
        $display = $tester->getDisplay();
        self::assertSame(file_get_contents(__DIR__."/../../../docs/console.md"), $display);
    }

    public function test_it_guards_env_is_bare()
    {
        TestCase::assertFalse(EnvironmentStateActual::get($this->container())->service()->isBare());

        $buildDocCommand = BuildDocCommandActual::get($this->container())->service();
        $buildDocCommand->setApplication($this->application()->console());

        $this->expectExceptionMessage("Environment is populated. This command needs to run on a bare environment.");
        $this->assertCommandExecutes($buildDocCommand);
    }
}
