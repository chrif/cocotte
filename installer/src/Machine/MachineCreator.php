<?php declare(strict_types=1);

namespace Chrif\Cocotte\Machine;

use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\Shell\ProcessRunner;
use Symfony\Component\Process\Process;

final class MachineCreator
{

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    /**
     * @var MachineState
     */
    private $machineState;

    /**
     * @var MachineName
     */
    private $machineName;

    /**
     * @var MachineStoragePath
     */
    private $machineStoragePath;

    /**
     * @var ApiToken
     */
    private $token;

    /**
     * @var Style
     */
    private $style;

    public function __construct(
        ProcessRunner $processRunner,
        MachineState $machineState,
        MachineName $machineName,
        MachineStoragePath $machineStoragePath,
        ApiToken $token,
        Style $style
    ) {
        $this->processRunner = $processRunner;
        $this->machineState = $machineState;
        $this->machineName = $machineName;
        $this->machineStoragePath = $machineStoragePath;
        $this->token = $token;
        $this->style = $style;
    }

    public function create()
    {
        $this->style->title("Creating a Docker machine on Digital Ocean named '{$this->machineName}'");

        if ($this->machineState->exists()) {
            throw new \Exception(
                "Error: a machine named {$this->machineName} already exists. Remove it or choose a ".
                "different machine name. Run the uninstall command to remove it completely."
            );
        }

        $process = new Process(
            'docker-machine -s "${MACHINE_STORAGE_PATH}" create \
                --driver digitalocean \
                --digitalocean-access-token "${DIGITAL_OCEAN_API_TOKEN}" \
                --engine-opt log-driver="json-file" \
                --engine-opt log-opt="max-size=1m" \
                --engine-opt log-opt="max-file=10" \
                "${MACHINE_NAME}"'
        );

        $process->setTimeout(300);

        $this->processRunner->mustRun($process);
    }

}