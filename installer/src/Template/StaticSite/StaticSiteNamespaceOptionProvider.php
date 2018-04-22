<?php declare(strict_types=1);

namespace Chrif\Cocotte\Template\StaticSite;

use Chrif\Cocotte\Console\OptionProvider;
use Chrif\Cocotte\Console\Style;
use Chrif\Cocotte\Console\StyledInputOption;
use Chrif\Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

class StaticSiteNamespaceOptionProvider implements OptionProvider
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
        return new StyledInputOption(
            StaticSiteNamespace::OPTION_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            $this->helpMessage(),
            Env::get(StaticSiteNamespace::STATIC_SITE_NAMESPACE)
        );
    }

    public function validate(string $value)
    {
        StaticSiteNamespace::fromString($value);
    }

    public function helpMessage(): string
    {
        return $this->style->optionHelp(
            "Static site namespace",
            [
                "Allowed characters are lowercase letters, digits and -. ".
                "Must match ".StaticSiteNamespace::REGEX,
            ]
        );
    }

    public function question(): Question
    {
        return new Question(
            $this->style->quittableQuestion("Choose a <options=bold>namespace for the site</>")
        );
    }

    public function onCorrectAnswer(string $answer)
    {

    }

    public function optionName(): string
    {
        return StaticSiteNamespace::OPTION_NAME;
    }

}