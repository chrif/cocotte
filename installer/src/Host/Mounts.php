<?php declare(strict_types=1);

namespace Chrif\Cocotte\Host;

use Assert\Assertion;
use Chrif\Cocotte\Environment\ImportableValue;
use Symfony\Component\Process\Process;

class Mounts implements ImportableValue
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
     * @return self|ImportableValue
     * @throws \Exception
     */
    public static function fromEnv(): ImportableValue
    {
        if (null === self::$mounts) {
            $process = new Process('docker inspect --format="{{json .Mounts}}" $HOSTNAME');
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \Exception(
                    $process->getErrorOutput()."\nMake sure you start Docker and mount the Docker ".
                    "socket with a volume like this:\n-v /var/run/docker.sock:/var/run/docker.sock:ro"
                );
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