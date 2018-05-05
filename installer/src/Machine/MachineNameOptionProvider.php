<?php declare(strict_types=1);

namespace Cocotte\Machine;

use Cocotte\Console\OptionProvider;
use Cocotte\Console\Style;
use Cocotte\Console\StyledInputOption;
use Cocotte\Environment\EnvironmentState;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class MachineNameOptionProvider implements OptionProvider
{
    /**
     * @var Style
     */
    private $style;

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    public function option(EnvironmentState $environmentState): InputOption
    {
        return new StyledInputOption(
            $this->optionName(),
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            $environmentState->defaultValue(MachineName::MACHINE_NAME, MachineName::DEFAULT_VALUE)
        );
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Machine Name",
            [
                "This is both the name used for <info>docker-machine</info> commands and by Digital Ocean\nfor the droplet name. ".
                "Must match ".MachineName::REGEX,
            ]
        );
    }

    public function validate(string $value)
    {
        MachineName::fromString($value);
    }

    public function onCorrectAnswer(string $answer)
    {
        // do nothing
    }

    public function optionName(): string
    {
        return MachineName::OPTION_NAME;
    }

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion("Enter a <options=bold>Machine name</>"),
            MachineName::DEFAULT_VALUE
        );
    }

}