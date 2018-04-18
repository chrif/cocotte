<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\Console\InteractionOperator;
use Chrif\Cocotte\Console\OptionProvider;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class TraefikPasswordOptionProvider implements OptionProvider
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
            TraefikPassword::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            Env::get(TraefikPassword::TRAEFIK_UI_PASSWORD)
        );
    }

    public function validate(string $value)
    {
        TraefikPassword::fromString($value);
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Traefik UI password",
            [
                "Alphanumeric and some special characters. Must match ".TraefikPassword::REGEX,
            ]
        );
    }

    public function optionName(): string
    {
        return TraefikPassword::OPTION_NAME;
    }

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion("Choose a <options=bold>password for your Traefik UI</>")
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