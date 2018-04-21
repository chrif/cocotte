<?php declare(strict_types=1);

namespace Chrif\Cocotte\Machine;

use Chrif\Cocotte\Console\OptionProvider;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Shell\Env;
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

    public function option(): InputOption
    {
        return new InputOption(
            MachineName::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            Env::get(MachineName::MACHINE_NAME)
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
            'cocotte'
        );
    }

}