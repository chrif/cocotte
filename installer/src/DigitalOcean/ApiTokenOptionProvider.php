<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Cocotte\Console\OptionProvider;
use Cocotte\Console\Style;
use Cocotte\Console\StyledInputOption;
use Cocotte\Environment\EnvironmentState;
use DigitalOceanV2\Adapter\GuzzleHttpAdapter;
use DigitalOceanV2\DigitalOceanV2;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class ApiTokenOptionProvider implements OptionProvider
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
            $environmentState->defaultValue(ApiToken::DIGITAL_OCEAN_API_TOKEN)
        );
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Digital Ocean API Token",
            [
                "If you don't have a Digital Ocean account yet, get one with a 10$ credit at\n".
                $this->style->link('https://m.do.co/c/c25ed78e51c5'),
                "Then generate a token at ".$this->style->link('https://cloud.digitalocean.com/settings/api/tokens'),
                "Cocotte will make a call to Digital Ocean's API to validate the token.",
            ]
        );
    }

    public function validate(string $value)
    {
        $token = new ApiToken($value);
        $adapter = new GuzzleHttpAdapter($token->toString());
        $digitalOceanV2 = new DigitalOceanV2($adapter);
        try {
            $domain = sprintf('cocotte-validate-%s.token', uniqid());
            $digitalOceanV2->domain()->create($domain, '127.0.0.1');
            $digitalOceanV2->domain()->delete($domain);
        } catch (\Throwable $e) {
            throw new \Exception(
                "Failed to validate that the Digital Ocean token has 'write' permissions. Error message was:\n".
                $e->getMessage()
            );
        }
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

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion("Enter your <options=bold>Digital Ocean API token</>")
        );
    }

}