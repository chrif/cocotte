<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

interface OptionProvider
{
    const OPTION_PROVIDER = 'option.provider';

    public function option(): InputOption;

    public function validate(string $value);

    public function helpMessage(): string;

    public function question(): Question;

    public function onCorrectAnswer(string $answer);

    public function optionName(): string;
}