<?php

declare(strict_types=1);

namespace Chrif\Cocotte;

use Chrif\Cocotte\Configuration\Droplet\DropletName;
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
        if (!exec("sh /installer/machine-is-running")) {
            self::fail("Machine is not running");
        }
    }

    protected function application(): Application
    {
        if (!$this->application) {
            $this->application = new Application(
                __DIR__.'/../config/services.yml',
                __DIR__.'/../config/cocotte_test.yml'
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

    protected function dropletName(): DropletName
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->application()->container()->get(DropletName::class);
    }

}