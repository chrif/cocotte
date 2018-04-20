<?php declare(strict_types=1);

namespace Chrif\Cocotte\Host;

use Assert\Assertion;
use Chrif\Cocotte\Environment\LazyEnvironmentValue;
use Symfony\Component\Process\Process;

class Mounts implements LazyEnvironmentValue
{
    /**
     * @var array
     */
    private static $mounts;

    /**
     * @var array
     */
    private $value;

    public function __construct(array $value)
    {
        Assertion::greaterOrEqualThan(count($value), 1);
        $this->value = $value;
    }

    /**
     * @return LazyEnvironmentValue|self
     * @throws HostException
     */
    public static function fromEnv(): LazyEnvironmentValue
    {
        if (null === self::$mounts) {
            $process = new Process('docker inspect --format="{{json .Mounts}}" $HOSTNAME');
            $process->run();
            if (!$process->isSuccessful()) {
                $error = $process->getErrorOutput();
                if (false !== strpos($error, 'var/run/docker.sock')) {
                    throw HostException::noSocketMount($process->getErrorOutput());
                } else {
                    throw new HostException($error);
                }
            }
            self::$mounts = json_decode($process->getOutput(), true);
        }

        return new self(self::$mounts);
    }

    public function toArray(): array
    {
        return $this->value;
    }

}