<?php declare(strict_types=1);

namespace Cocotte\Console;

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

    public function interact(InputInterface $input, OptionProvider $optionProvider)
    {
        $name = $optionProvider->optionName();

        if (!$input->hasOption($name)) {
            throw new \Exception("input does not have option '$name'");
        }

        try {
            $optionProvider->validate($input->getOption($name) ?? "");
        } catch (\Throwable $e) {
            $this->style->error($e->getMessage());
            $input->setOption($name, $this->ask($optionProvider));
        }
    }

    public function ask(OptionProvider $optionProvider): string
    {
        $this->style->help($optionProvider->helpMessage());

        return $this->style->askQuestion(
            $optionProvider
                ->question()
                ->setNormalizer($this->normalizer())
                ->setValidator($this->validator($optionProvider))
        );
    }

    private function normalizer(): \Closure
    {
        return function ($answer): string {
            return trim((string)$answer);
        };
    }

    private function validator(OptionProvider $optionProvider): \Closure
    {
        return function (string $answer) use ($optionProvider): string {
            if (!$answer) {
                throw new \Exception('No answer was given. Try again.');
            }

            $optionProvider->validate($answer);
            $optionProvider->onCorrectAnswer($answer);

            return $answer;
        };
    }

}