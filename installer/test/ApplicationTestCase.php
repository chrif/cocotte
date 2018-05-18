<?php declare(strict_types=1);

namespace Cocotte\Test;

use Cocotte\DependencyInjection\Application;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Environment\LazyEnvironmentLoader;
use Cocotte\Test\Collaborator\Console\HasAllOptionsWithNullValuesInputStub;
use Cocotte\Test\Collaborator\Console\InputActual;
use Cocotte\Test\Collaborator\Console\OutputActual;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;

/**
 * This is the base class for all system tests.
 */
class ApplicationTestCase extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    public static function assertEnvInString($expected, $actual)
    {
        $process = new Process('envsubst');
        $process->setInput($expected);
        $process->mustRun();
        self::assertSame($process->getOutput(), $actual);
    }

    protected function assertCommandExecutes(
        Command $command,
        array $options = [],
        $guardDisplay = true
    ): CommandTester {
        $input = InputActual::get($this->container())->service();
        $output = OutputActual::get($this->container())->service();
        $verbosity = (int)(getenv('SYSTEM_TEST_VERBOSITY') ?: OutputInterface::VERBOSITY_DEBUG);
        self::assertContains($verbosity,
            [
                OutputInterface::VERBOSITY_QUIET,
                OutputInterface::VERBOSITY_DEBUG,
            ],
            "The verbosity modes suited for system tests are quiet or debug. $verbosity was used.");

        $input->setInteractive(false);
        $output->setVerbosity($verbosity);

        $tester = new CommandTester($command);
        $tester->execute(
            $options,
            [
                'interactive' => false,
            ]
        );

        self::assertSame(0, $tester->getStatusCode());
        if ($guardDisplay && trim($tester->getDisplay())) {
            self::fail(
                "The memory output has been used during tests. It means some code did not use the output ".
                "service to write to console, but rather the output parameter directly. Make sure it is ".
                "a desired behavior. In production, the output service and the output instance passed ".
                "as parameter to a command are the same instance, but injecting the output service is".
                " the preferred way of accessing it for testing purposes."
            );
        }

        return $tester;
    }

    protected function application(): Application
    {
        if (!$this->application) {
            $this->application = new Application(
                __DIR__.'/../config/services.yml',
                [$this->compilerPass()]
            );
        }

        return $this->application;
    }

    protected function container(): ContainerInterface
    {
        return $this->application()->container();
    }

    protected function loadEnvironment()
    {
        if ($this instanceof LazyEnvironment) {
            $this->environmentLoader()->load($this, new HasAllOptionsWithNullValuesInputStub());
        } else {
            throw new \Exception(get_class($this).' does not implement '.LazyEnvironment::class);
        }
    }

    /**
     * @return TestCompilerPass
     */
    protected function compilerPass(): TestCompilerPass
    {
        return new TestCompilerPass(true);
    }

    private function environmentLoader(): LazyEnvironmentLoader
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container()->get(LazyEnvironmentLoader::class);
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    /**
     * Not used but might be useful to have around at some point.
     *
     * @param string $display
     * @return string
     */
    private function normalizeDisplay(string $display): string
    {
        /**
         * strip container output line prefix:
         *
         * Sample:
         *
         * cmd_1          |
         * cmd_1          | cmd_1          |
         * cmd_1          | cmd_1          |
         * cmd_1          |
         *
         */
        // https://github.com/moby/moby/blob/master/daemon/names/names.go#L6
        $containerName = '[a-zA-Z0-9][a-zA-Z0-9_\.-]';
        $space = ' ';
        $display = preg_replace("/^({$containerName}{$space}{10}|{$space})+/", '', $display);

        return $display;
    }
}