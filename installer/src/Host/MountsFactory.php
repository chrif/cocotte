<?php declare(strict_types=1);

namespace Cocotte\Host;

class MountsFactory
{
    /**
     * @var array
     */
    private $mounts;

    /**
     * @var InspectMountsProcess
     */
    private $process;

    public function __construct(InspectMountsProcess $process)
    {
        $this->process = $process;
    }

    /**
     * @return Mounts
     * @throws HostException
     */
    public function fromDocker(): Mounts
    {
        if (null === $this->mounts) {
            $this->process->run();
            if (!$this->process->isSuccessful()) {
                $error = $this->process->getErrorOutput();
                if (false !== strpos($error, 'var/run/docker.sock')) {
                    throw HostException::noSocketMount($this->process->getErrorOutput());
                } else {
                    throw new HostException($error);
                }
            }
            $this->mounts = json_decode($this->process->getOutput(), true);
        }

        return new Mounts($this->mounts);
    }

}