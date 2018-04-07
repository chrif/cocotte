<?php declare(strict_types=1);

namespace Chrif\Cocotte;

use Chrif\Cocotte\Configuration\MachineName;
use Chrif\Cocotte\DependencyInjection\Application;
use Chrif\Cocotte\DigitalOcean\Domain;
use Chrif\Cocotte\DigitalOcean\DomainRecord;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Application
     */
    private $application;

    public static function setUpBeforeClass()
    {
        if (!exec("machine-is-running")) {
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