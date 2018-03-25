<?php

declare(strict_types=1);

namespace Chrif\Cocotte;

use Chrif\Cocotte\Configuration\App\AppHost;
use Chrif\Cocotte\Configuration\App\AppHostCollection;
use Chrif\Cocotte\DependencyInjection\Application;
use Chrif\Cocotte\DigitalOcean\Domain;
use Chrif\Cocotte\DigitalOcean\DomainRecord;

class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Application
     */
    private $application;

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

    protected function appHostCollection(): AppHostCollection
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->application()->container()->get(AppHostCollection::class);
    }

    protected function appHost(): AppHost
    {
        return $this->appHostCollection()->offsetGet(0);
    }

}