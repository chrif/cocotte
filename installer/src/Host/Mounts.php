<?php declare(strict_types=1);

namespace Chrif\Cocotte\Host;

use Assert\Assertion;
use Symfony\Component\Process\Process;

final class Mounts
{
    /**
     * @var array
     */
    private $mounts;

    public function toArray(): array
    {
        if (null === $this->mounts) {
            $process = new Process('docker inspect --format="{{json .Mounts}}" $HOSTNAME');
            $process->run();
            if (!$process->isSuccessful()) {
                throw new \Exception(
                    $process->getErrorOutput()."\nMake sure you start Docker and mount the Docker ".
                    "socket with a volume like this:\n-v /var/run/docker.sock:/var/run/docker.sock:ro"
                );
            }
            $mounts = json_decode($process->getOutput(), true);
            Assertion::isArray($mounts);
            Assertion::greaterOrEqualThan(count($mounts), 1);
            $this->mounts = $mounts;
        }

        return $this->mounts;
    }

}