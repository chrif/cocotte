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

    protected function assertCommandExecutes(Command $command, array $options = []): CommandTester
    {
        $input = InputActual::get($this->container())->service();
        $output = OutputActual::get($this->container())->service();

        $input->setInteractive(false);
        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $tester = new CommandTester($command);
        $tester->execute(
            $options,
            [
                'interactive' => false,
                'verbosity' => OutputInterface::VERBOSITY_DEBUG,
            ]
        );

        self::assertSame(0, $tester->getStatusCode());

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