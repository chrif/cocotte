<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\Console\InteractionOperator;
use Chrif\Cocotte\Console\OptionInteraction;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class TraefikUsernameInteraction implements OptionInteraction
{
    /**
     * @var Style
     */
    private $style;
    /**
     * @var InteractionOperator
     */
    private $operator;

    public function __construct(Style $style, InteractionOperator $operator)
    {
        $this->style = $style;
        $this->operator = $operator;
    }

    public function option(): InputOption
    {
        return new InputOption(
            TraefikUsername::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            Env::get(TraefikUsername::TRAEFIK_UI_USERNAME)
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

    public function interact(InputInterface $input)
    {
        $this->operator->interact($input, $this);
    }

    public function ask(): string
    {
        return $this->operator->ask($this);
    }

}