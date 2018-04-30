<?php declare(strict_types=1);

namespace Cocotte\Test;

use Cocotte\DependencyInjection\Application;
use Cocotte\DigitalOcean\Domain;
use Cocotte\DigitalOcean\DomainRecord;
use Cocotte\Machine\MachineName;
use Cocotte\Machine\MachineState;
use PHPUnit\Framework\TestCase;

class FunctionalTestCase extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    public static function setUpBeforeClass()
    {
        if (!MachineState::fromEnv()->isRunning()) {
            self::fail("Machine is not running");
        }
    }

    protected function application(): Application
    {
        if (!$this->application) {
            $this->application = new Application(
                __DIR__.'/../config/services_test.yml'
            );
        }

        return $this->application;
    }

    protected function domainApi(): Domain
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->application()->container()->get(Domain::class);
    }

    protected function domainRecordApi(): DomainRecord
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->application()->container()->get(DomainRecord::class);
    }

    protected function machineName(): MachineName
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->application()->container()->get(MachineName::class);
    }

}