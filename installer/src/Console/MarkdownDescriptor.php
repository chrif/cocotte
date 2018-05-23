<?php declare(strict_types=1);

namespace Cocotte\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarkdownDescriptor
{

    /**
     * @var OutputInterface
     */
    private $output;

    public function describe(OutputInterface $output, Application $object)
    {
        $decorated = $output->isDecorated();
        $output->setDecorated(false);

        $this->output = $output;

        $this->describeApplication($object);

        $output->setDecorated($decorated);
    }

    /**
     * Writes content to output.
     *
     * @param string $content
     * @param bool $decorated
     */
    private function write($content, $decorated = false)
    {
        $this->output->write($content,
            false,
            $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
    }

    private function describeInputArgument(InputArgument $argument)
    {
        $description = $this->removeDecoration($argument->getDescription());
        $this->write(
            '#### `'.$this->argumentName($argument)."`\n\n"
            .$this->formatArgumentDescription($description)
            .'* Is required: '.$this->argumentIsRequired($argument)."\n"
            .'* Is array: '.$this->argumentIsArray($argument)."\n"
            .'* Default: `'.str_replace("\n", '', var_export($argument->getDefault(), true)).'`'
        );
    }

    private function describeInputOption(InputOption $option)
    {
        $name = '--'.$option->getName();
        if ($option->getShortcut()) {
            $name .= '|-'.implode('|-', explode('|', $option->getShortcut())).'';
        }

        $emphasis = $this->optionEmphasis($option);

        $description = $this->removeDecoration($option->getDescription());
        $this->write(
            '#### `'.$name.'`'."\n\n"
            .$this->formatOptionDescription($description, $emphasis)
            .'* Accept value: '.$this->optionValue($option)."\n"
            .'* Is value required: '.$this->optionValueRequired($option)."\n"
            .'* Is multiple: '.$this->optionIsMultiple($option)."\n"
            .'* Default: `'.str_replace("\n", '', var_export($option->getDefault(), true)).'`'
        );
    }

    private function describeInputDefinition(InputDefinition $definition)
    {
        $showArguments = $this->describeInputDefinitionArguments($definition);

        $this->describeInputDefinitionOptions($definition, $showArguments);
    }

    private function describeCommand(Command $command)
    {
        $synopsis = $command->getSynopsis(true);

        $this->write(
            $command->getName()."\n"
            .str_repeat('-', Helper::strlen($command->getName()) + 2)."\n\n"
            .'### Usage'."\n\n"
            .$this->commandUsage($command, $synopsis)
        );

        if ($help = $command->getProcessedHelp()) {
            $this->write("\n");
            $this->write($this->removeDecoration($help));
        }

        if ($command->getNativeDefinition()) {
            $this->write("\n\n");
            $this->describeInputDefinition($command->getNativeDefinition());
        }
    }

    private function describeApplication(Application $application)
    {
        $description = new \Symfony\Component\Console\Descriptor\ApplicationDescription($application);
        $title = "Console API Reference";

        $this->write($title."\n".str_repeat('=', Helper::strlen($title)));

        $this->tableOfContents($description);

        foreach ($this->filterCommands($description) as $command) {
            $this->write("\n\n---\n\n");
            $this->describeCommand($command);
        }
    }

    private function removeDecoration(string $string): string
    {
        $f = new OutputFormatter();

        return $f->format($string);
    }

    /**
     * @param InputDefinition $definition
     * @return array|InputOption[]
     */
    private function getInputOptions(InputDefinition $definition)
    {
        $options = $definition->getOptions();
        $options = $this->filterOptions($options);
        usort($options, $this->sortOptions());

        return $options;
    }

    /**
     * @param $description
     * @param $emphasis
     * @return string
     */
    private function formatOptionDescription($description, $emphasis): string
    {
        return ($description ? $emphasis.preg_replace('/\s*[\r\n]\s*/', "\n", $description)."\n\n" : '');
    }

    /**
     * @param InputOption $option
     * @return string
     */
    private function optionValue(InputOption $option): string
    {
        return ($option->acceptValue() ? 'yes' : 'no');
    }

    /**
     * @param InputOption $option
     * @return string
     */
    private function optionValueRequired(InputOption $option): string
    {
        return ($option->isValueRequired() ? 'yes' : 'no');
    }

    /**
     * @param InputOption $option
     * @return string
     */
    private function optionIsMultiple(InputOption $option): string
    {
        return ($option->isArray() ? 'yes' : 'no');
    }

    /**
     * @param InputOption $option
     * @return string
     */
    private function optionEmphasis(InputOption $option): string
    {
        return $option instanceof StyledInputOption ? '##### ' : '';
    }

    /**
     * @param InputArgument $argument
     * @return string
     */
    private function argumentIsRequired(InputArgument $argument): string
    {
        return ($argument->isRequired() ? 'yes' : 'no');
    }

    /**
     * @param InputArgument $argument
     * @return string
     */
    private function argumentIsArray(InputArgument $argument): string
    {
        return ($argument->isArray() ? 'yes' : 'no');
    }

    /**
     * @param InputArgument $argument
     * @return string
     */
    private function argumentName(InputArgument $argument): string
    {
        return ($argument->getName() ?: '<none>');
    }

    /**
     * @param $description
     * @return string
     */
    private function formatArgumentDescription($description): string
    {
        return ($description ? preg_replace('/\s*[\r\n]\s*/',
                "\n",
                $description)."\n\n" : '');
    }

    /**
     * @param InputDefinition $definition
     * @return bool
     */
    private function describeInputDefinitionArguments(InputDefinition $definition): bool
    {
        if ($showArguments = count($definition->getArguments()) > 0) {
            $this->write('### Arguments');
            foreach ($definition->getArguments() as $argument) {
                $this->write("\n\n");
                $this->describeInputArgument($argument);
            }
        }

        return $showArguments;
    }

    /**
     * @param InputDefinition $definition
     * @param $showArguments
     */
    private function describeInputDefinitionOptions(InputDefinition $definition, $showArguments): void
    {
        $inputOptions = $this->getInputOptions($definition);
        if (count($inputOptions) > 0) {
            if ($showArguments) {
                $this->write("\n\n");
            }

            $this->write('### Options');
            foreach ($inputOptions as $option) {
                $this->write("\n\n");
                $this->describeInputOption($option);
            }
        }
    }

    /**
     * @param \Symfony\Component\Console\Descriptor\ApplicationDescription $description
     * @return array
     */
    private function filterCommands($description): array
    {
        return array_filter($description->getCommands(),
            function (Command $command) {
                return $command instanceof DocumentedCommand;
            });
    }

    /**
     * @param \Symfony\Component\Console\Descriptor\ApplicationDescription $description
     */
    private function tableOfContents($description): void
    {
        $this->write("\n\n");
        $this->write(implode("\n",
                array_map(
                    function (Command $command) {
                        return sprintf(
                            "* [`%s`](#%s)\n  > %s",
                            $command->getName(),
                            str_replace(':', '', $command->getName()),
                            $this->removeDecoration($command->getDescription())
                        );
                    },
                    $this->filterCommands($description)
                )
            )
        );
    }

    /**
     * @param $options
     * @return array
     */
    private function filterOptions($options): array
    {
        return array_filter($options,
            function (InputOption $option) {
                return !in_array(
                    $option->getName(),
                    ['help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction']
                );
            });
    }

    /**
     * @return \Closure
     */
    private function sortOptions(): \Closure
    {
        return function (InputOption $a, InputOption $b) {
            if ($a->isValueRequired() == $b->isValueRequired()) {
                return 0;
            }
            if ($a->isValueRequired()) {
                return -1;
            }

            return 1;
        };
    }

    /**
     * @param Command $command
     * @param $synopsis
     * @return string
     */
    private function commandUsage(Command $command, $synopsis): string
    {
        return array_reduce(array_merge(array($synopsis),
            $command->getAliases(),
            $command->getUsages()),
            function ($carry, $usage) {
                return $carry.'* `'.$usage.'`'."\n";
            });
    }
}
