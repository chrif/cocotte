<?php declare(strict_types=1);

namespace Cocotte\Environment;

use Symfony\Component\EventDispatcher\Event;

final class EnvironmentLoadedEvent extends Event
{

    /**
     * @var LazyEnvironment
     */
    private $environment;

    public function __construct(LazyEnvironment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return LazyEnvironment
     */
    public function environment(): LazyEnvironment
    {
        return $this->environment;
    }

}