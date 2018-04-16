<?php declare(strict_types=1);

namespace Chrif\Cocotte\DigitalOcean;

use Chrif\Cocotte\Console\InteractionOperator;
use Chrif\Cocotte\Console\OptionInteraction;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class ApiTokenInteraction implements OptionInteraction
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
            ApiToken::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            Env::get(ApiToken::DIGITAL_OCEAN_API_TOKEN)
        );
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Digital Ocean API Token",
            [
                "If you don't have a Digital Ocean account yet, get one with a 10$ credit at\n".
                $this->style->link('digitalocean.com/?refcode=c25ed78e51c5')."",
                "Then generate a token at ".$this->style->link('cloud.digitalocean.com/settings/api/tokens'),
                "Cocotte will make a call to Digital Ocean's API to validate the token.",
            ]
        );
    }

    public function validate(string $value)
    {
        $token = new ApiToken($value);
        $token->assertAccountIsActive();
    }

    public function onCorrectAnswer(string $answer)
    {
        $this->style->success("Token '$answer' is valid");
        $this->style->pause();
    }

    public function optionName(): string
    {
        return ApiToken::OPTION_NAME;
    }

    public function interact(InputInterface $input)
    {
        $this->operator->interact($input, $this);
    }

    public function ask(): string
    {
        return $this->operator->ask($this);
    }

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion("Enter your <options=bold>Digital Ocean API token</>")
        );
    }
}