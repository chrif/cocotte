<?php declare(strict_types=1);

namespace Cocotte\Console;

final class CommandEventStore
{

    const COMMAND_BEFORE_INITIALIZE = 'command.before_initialize';
    const COMMAND_CONFIGURE = 'command.configure';
    const COMMAND_CONFIGURED = 'command.configured';
    const COMMAND_EXECUTE = 'command.execute';
    const COMMAND_INITIALIZE = 'command.initialize';
    const COMMAND_INTERACT = 'command.interact';
}
