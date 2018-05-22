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
            '#### `'.($argument->getName() ?: '<none>')."`\n\n"
            .($description ? preg_replace('/\s*[\r\n]\s*/',
                    "\n",
                    $description)."\n\n" : '')
            .'* Is required: '.($argument->isRequired() ? 'yes' : 'no')."\n"
            .'* Is array: '.($argument->isArray() ? 'yes' : 'no')."\n"
            .'* Default: `'.str_replace("\n", '', var_export($argument->getDefault(), true)).'`'
        );
    }

    private function describeInputOption(InputOption $option)
    {
        $name = '--'.$option->getName();
        if ($option->getShortcut()) {
            $name .= '|-'.implode('|-', explode('|', $option->getShortcut())).'';
        }

        $emphasis = $option instanceof StyledInputOption ? '##### ' : '';

        $description = $this->removeDecoration($option->getDescription());
        $this->write(
            '#### `'.$name.'`'."\n\n"
            .($description ? $emphasis.preg_replace('/\s*[\r\n]\s*/', "\n", $description)."\n\n" : '')
            .'* Accept value: '.($option->acceptValue() ? 'yes' : 'no')."\n"
            .'* Is value required: '.($option->isValueRequired() ? 'yes' : 'no')."\n"
            .'* Is multiple: '.($option->isArray() ? 'yes' : 'no')."\n"
            .'* Default: `'.str_replace("\n", '', var_export($option->getDefault(), true)).'`'
        );
    }

    private function describeInputDefinition(InputDefinition $definition)
    {
        if ($showArguments = count($definition->getArguments()) > 0) {
            $this->write('### Arguments');
            foreach ($definition->getArguments() as $argument) {
                $this->write("\n\n");
                $this->describeInputArgument($argument);
            }
        }

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

    private function describeCommand(Command $command)
    {
        $synopsis = $command->getSynopsis(true);

        $this->write(
            $command->getName()."\n"
            .str_repeat('-', Helper::strlen($command->getName()) + 2)."\n\n"
            .'### Usage'."\n\n"
            .array_reduce(array_merge(array($synopsis),
                $command->getAliases(),
                $command->getUsages()),
                function ($carry, $usage) {
                    return $carry.'* `'.$usage.'`'."\n";
                })
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

        $commands = array_filter($description->getCommands(),
            function (Command $command) {
                return $command instanceof DocumentedCommand;
            });

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
                    $commands
                )
            )
        );

        foreach ($commands as $command) {
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
        $options = array_filter($options,
            function (InputOption $option) {
                return !in_array(
                    $option->getName(),
                    ['help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction']
                );
            });
        usort($options,
            function (InputOption $a, InputOption $b) {
                $aRequired = $a->isValueRequired() ? 1 : 0;
                $bRequired = $b->isValueRequired() ? 1 : 0;
                if ($aRequired == $bRequired) {
                    return 0;
                }

                return ($aRequired > $bRequired) ? -1 : 1;
            });

        return $options;
    }
}
