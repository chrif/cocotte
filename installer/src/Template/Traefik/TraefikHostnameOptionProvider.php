<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\Traefik;

use Chrif\Cocotte\Console\InteractionOperator;
use Chrif\Cocotte\Console\OptionProvider;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\DigitalOcean\DnsValidator;
use Chrif\Cocotte\DigitalOcean\Hostname;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputInterface;
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

    /**
     * @var InteractionOperator
     */
    private $operator;

    public function __construct(Style $style, DnsValidator $dnsValidator, InteractionOperator $operator)
    {
        $this->style = $style;
        $this->dnsValidator = $dnsValidator;
        $this->operator = $operator;
    }

    public function option(): InputOption
    {
        return new InputOption(
            TraefikHostname::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            Env::get(TraefikHostname::TRAEFIK_UI_HOSTNAME)
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
        $hostname = Hostname::parse($value);

        $this->dnsValidator->validateHost($hostname);
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

    public function interact(InputInterface $input)
    {
        $this->operator->interact($input, $this);
    }

    public function ask(): string
    {
        return $this->operator->ask($this);
    }

}