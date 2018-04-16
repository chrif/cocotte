<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

interface OptionInteraction
{
    public function option(): InputOption;

    public function interact(InputInterface $input);

    public function ask(): string;

    public function validate(string $value);

    public function helpMessage(): string;

    public function question(): Question;

    public function onCorrectAnswer(string $answer);

    public function optionName(): string;
}