<?php declare(strict_types=1);

namespace Cocotte\Template\Traefik;

use Cocotte\Console\OptionProvider;
use Cocotte\Console\Style;
use Cocotte\Console\StyledInputOption;
use Cocotte\Environment\EnvironmentState;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class TraefikUsernameOptionProvider implements OptionProvider
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
            $environmentState->defaultValue(TraefikUsername::TRAEFIK_UI_USERNAME)
        );
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Traefik UI username",
            [
                "Alphanumeric characters. ".
                "Must match ".TraefikUsername::REGEX,
            ]
        );
    }

    public function validate(string $value)
    {
        TraefikUsername::fromString($value);
    }

    public function optionName(): string
    {
        return TraefikUsername::OPTION_NAME;
    }

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion("Choose a <options=bold>username for your Traefik UI</>"),
            TraefikUsername::SUGGESTED_VALUE
        );
    }

    public function onCorrectAnswer(string $answer)
    {
        // do nothing
    }

}