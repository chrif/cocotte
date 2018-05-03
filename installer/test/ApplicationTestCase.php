<?php declare(strict_types=1);

namespace Cocotte\Test;

use Cocotte\DependencyInjection\Application;
use Cocotte\Environment\LazyEnvironment;
use Cocotte\Environment\LazyEnvironmentLoader;
use Cocotte\Test\Collaborator\Console\HasAllOptionsWithNullValuesInputStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplicationTestCase extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function assertCommandExecutes(Command $command, array $input = []): CommandTester
    {
        $this->container()->get(InputInterface::class)->setInteractive(false);
        $this->container()->get(OutputInterface::class)->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $tester = new CommandTester($command);
        $tester->execute(
            $input,
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
                __DIR__.'/../config/services_test.yml'
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

    private function environmentLoader(): LazyEnvironmentLoader
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container()->get(LazyEnvironmentLoader::class);
    }
}