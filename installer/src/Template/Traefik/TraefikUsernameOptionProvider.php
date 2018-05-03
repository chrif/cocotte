<?php declare(strict_types=1);

namespace Cocotte\Template\Traefik;

use Cocotte\Console\OptionProvider;
use Cocotte\Console\Style;
use Cocotte\Console\StyledInputOption;
use Cocotte\Shell\Env;
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

    public function option(Env $env): InputOption
    {
        return new StyledInputOption(
            TraefikUsername::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            $env->get(TraefikUsername::TRAEFIK_UI_USERNAME)
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
            "admin"
        );
    }

    public function onCorrectAnswer(string $answer)
    {
        // do nothing
    }

}