<?php declare(strict_types=1);

namespace Cocotte\Host;

use Symfony\Component\Process\Process;

class MountsFactory
{
    /**
     * @var array
     */
    private $mounts;

    /**
     * @return Mounts
     * @throws HostException
     */
    public function fromDocker(): Mounts
    {
        if (null === $this->mounts) {
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
            $this->mounts = json_decode($process->getOutput(), true);
        }

        return new Mounts($this->mounts);
    }

}