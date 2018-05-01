<?php declare(strict_types=1);

namespace Cocotte\Test;

use Cocotte\DependencyInjection\Application;
use Cocotte\DigitalOcean\Domain;
use Cocotte\DigitalOcean\DomainRecord;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Environment\LazyEnvironmentLoader;
use Cocotte\Machine\MachineName;
use Cocotte\Test\Double\Console\HasAllOptionsWithNullValuesInput;
use PHPUnit\Framework\TestCase;

class FunctionalTestCase extends TestCase
{
    /**
     * @var Application
     */
    private $application;

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

    protected function environmentLoader(): LazyEnvironmentLoader
    {
        return $this->application()->container()->get(LazyEnvironmentLoader::class);
    }

    protected function loadEnvironment()
    {
        if ($this instanceof LazyEnvironment) {
            $this->environmentLoader()->load($this, new HasAllOptionsWithNullValuesInput());
        } else {
            throw new \Exception(get_class($this).' does not implement '.LazyEnvironment::class);
        }
    }
}