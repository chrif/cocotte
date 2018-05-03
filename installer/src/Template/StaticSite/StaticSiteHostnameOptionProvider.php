<?php declare(strict_types=1);

namespace Cocotte\Template\StaticSite;

use Cocotte\Console\OptionProvider;
use Cocotte\Console\Style;
use Cocotte\Console\StyledInputOption;
use Cocotte\DigitalOcean\DnsValidator;
use Cocotte\DigitalOcean\Hostname;
use Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class StaticSiteHostnameOptionProvider implements OptionProvider
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

    public function option(Env $env): InputOption
    {
        return new StyledInputOption(
            StaticSiteHostname::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            $env->get(StaticSiteHostname::STATIC_SITE_HOSTNAME)
        );
    }

    public function validate(string $value)
    {
        $hostname = Hostname::parse($value);

        $this->dnsValidator->validateHost($hostname);
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Static site hostname",
            $this->style->hostnameHelp('website', 'mywebsite')
        );
    }

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion("Choose a <options=bold>hostname for the site</> (e.g., mysite.mydomain.com)")
        );
    }

    public function onCorrectAnswer(string $answer)
    {
        // do nothing
    }

    public function optionName(): string
    {
        return StaticSiteHostname::OPTION_NAME;
    }

}