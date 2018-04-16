<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Input\InputInterface;

final class InteractionOperator
{
    /**
     * @var Style
     */
    private $style;

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    public function interact(InputInterface $input, OptionInteraction $interaction)
    {
        $name = $interaction->optionName();

        if (!$input->hasOption($name)) {
            throw new \Exception("input does not have option '$name'");
        }

        try {
            $interaction->validate($input->getOption($name) ?? "");
        } catch (\Exception $e) {
            $this->style->error($e->getMessage());
            $input->setOption($name, $interaction->ask());
        }
    }

    public function ask(OptionInteraction $interaction): string
    {
        $this->style->help($interaction->helpMessage());

        return $this->style->askQuestion(
            $interaction->question()
                ->setNormalizer($this->normalizer())
                ->setValidator($this->validator($interaction))
        );
    }

    private function normalizer(): \Closure
    {
        return function ($answer): string {
            return trim((string)$answer);
        };
    }

    private function validator(OptionInteraction $interaction): \Closure
    {
        return function (string $answer) use ($interaction): string {
            if (!$answer) {
                throw new \Exception('No answer was given. Try again.');
            }

            $interaction->validate($answer);
            $interaction->onCorrectAnswer($answer);

            return $answer;
        };
    }

}