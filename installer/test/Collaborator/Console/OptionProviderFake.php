<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

use Cocotte\Console\OptionProvider;
use Cocotte\Environment\EnvironmentState;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;

final class OptionProviderFake implements OptionProvider
{
    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function option(EnvironmentState $environmentState): InputOption
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function validate(string $value)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function helpMessage(): string
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function question(): Question
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function onCorrectAnswer(string $answer)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function optionName(): string
    {
        return $this->name;
    }

}