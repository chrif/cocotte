<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\Console;

use Cocotte\Console\CommandInteractEvent;
use Cocotte\Console\OptionProviderAutomation;
use Cocotte\Console\OptionProviderRegistry;
use Cocotte\Test\Collaborator\Console\CommandInterfaceDouble;
use Cocotte\Test\Collaborator\Console\InteractionOperatorDouble;
use Cocotte\Test\Collaborator\Console\OptionProviderFake;
use Cocotte\Test\Collaborator\Environment\EnvironmentStateDouble;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class OptionProviderAutomationTest extends TestCase
{

    public function test_it_interacts_with_registered_providers_on_command_interact()
    {
        $command = CommandInterfaceDouble::create($this)->optionProvidersMock([OptionProviderFake::class]);

        $provider = new OptionProviderFake('foo');

        $registry = new OptionProviderRegistry();
        $registry->registerProvider($provider);

        $input = new ArrayInput([], new InputDefinition([new InputOption('foo')]));

        $operator = InteractionOperatorDouble::create($this)->mock();
        $operator->expects(self::once())
            ->method('interact')
            ->with($input, $provider);

        $optionProviderAutomation = new OptionProviderAutomation(
            $registry,
            $operator,
            EnvironmentStateDouble::create($this)->mock()
        );

        $optionProviderAutomation->onCommandInteract(new CommandInteractEvent($command, $input));
    }
}
