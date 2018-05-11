<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Machine;

use Cocotte\Console\Style;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Machine\MachineCreator;
use Cocotte\Machine\MachineName;
use Cocotte\Machine\MachineState;
use Cocotte\Shell\ProcessRunner;
use Cocotte\Test\Collaborator\Console\StyleDouble;
use Cocotte\Test\Collaborator\DigitalOcean\ApiTokenFixture;
use Cocotte\Test\Collaborator\Shell\ProcessRunnerDouble;
use PHPUnit\Framework\TestCase;

final class MachineCreatorDoubleBuilder
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
     * @var ApiToken
     */
    private $token;

    /**
     * @var Style
     */
    private $style;

    public function __construct(TestCase $testCase)
    {
        $this->processRunner = ProcessRunnerDouble::create($testCase)->mock();
        $this->machineState = MachineStateDouble::create($testCase)->mock();
        $this->machineName = MachineNameFixture::fixture();
        $this->token = ApiTokenFixture::fixture();
        $this->style = StyleDouble::create($testCase)->mock();
    }

    public function setProcessRunner(ProcessRunner $processRunner): self
    {
        $this->processRunner = $processRunner;

        return $this;
    }

    public function setMachineState(MachineState $machineState): self
    {
        $this->machineState = $machineState;

        return $this;
    }

    public function setMachineName(MachineName $machineName): self
    {
        $this->machineName = $machineName;

        return $this;
    }

    public function setToken(ApiToken $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function setStyle(Style $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function newInstance(): MachineCreator
    {
        return new MachineCreator(
            $this->processRunner,
            $this->machineState,
            $this->machineName,
            $this->token,
            $this->style
        );
    }

    /**
     * @return ProcessRunner
     */
    public function processRunner(): ProcessRunner
    {
        return $this->processRunner;
    }

    /**
     * @return MachineState
     */
    public function machineState(): MachineState
    {
        return $this->machineState;
    }

    /**
     * @return MachineName
     */
    public function machineName(): MachineName
    {
        return $this->machineName;
    }

    /**
     * @return ApiToken
     */
    public function token(): ApiToken
    {
        return $this->token;
    }

    /**
     * @return Style
     */
    public function style(): Style
    {
        return $this->style;
    }
}