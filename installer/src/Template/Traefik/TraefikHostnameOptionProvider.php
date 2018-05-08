<?php declare(strict_types=1);

namespace Cocotte\Template\Traefik;

use Cocotte\Console\OptionProvider;
use Cocotte\Console\Style;
use Cocotte\Console\StyledInputOption;
use Cocotte\DigitalOcean\DnsValidator;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\Environment\EnvironmentState;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class TraefikHostnameOptionProvider implements OptionProvider
{
    /**
     * @var Style
     */
    private $style;

    /**
     * @var DnsValidator
     */
    private $dnsValidator;

    public function __construct(Style $style, DnsValidator $dnsValidator)
    {
        $this->style = $style;
        $this->dnsValidator = $dnsValidator;
    }

    public function option(EnvironmentState $environmentState): InputOption
    {
        return new StyledInputOption(
            $this->optionName(),
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            $environmentState->defaultValue(TraefikHostname::TRAEFIK_UI_HOSTNAME)
        );
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Traefik UI hostname",
            $this->style->hostnameHelp('Traefik UI', 'traefik')
        );
    }

    public function validate(string $value)
    {
        $traefikHostname = new TraefikHostname(Hostname::parse($value));

        $this->dnsValidator->validateHost($traefikHostname->toHostname());
    }

    public function onCorrectAnswer(string $answer)
    {
        $this->style->success("Traefik UI hostname '$answer' is valid.");
        $this->style->pause();
    }

    public function optionName(): string
    {
        return TraefikHostname::OPTION_NAME;
    }

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion(
                "Enter the <options=bold>Traefik UI hostname</> (e.g., traefik.mydomain.com)"
            )
        );
    }

}