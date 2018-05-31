<?php declare(strict_types=1);

namespace Cocotte\Console\Documentation;

use Cocotte\Console\DocumentedCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarkdownDocumentation
{

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var OptionDescriber
     */
    private $optionDescriber;

    /**
     * @var ArgumentDescriber
     */
    private $argumentDescriber;
    /**
     * @var LinkConverter
     */
    private $linkConverter;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->optionDescriber = new OptionDescriber();
        $this->argumentDescriber = new ArgumentDescriber();
        $this->linkConverter = new LinkConverter();
    }

    public function document(Application $object)
    {
        $decorated = $this->output->isDecorated();
        $this->output->setDecorated(false);

        $this->describeApplication($object);

        $this->output->setDecorated($decorated);
    }

    /**
     * Writes content to output.
     *
     * @param string $content
     * @param bool $decorated
     */
    private function write($content, $decorated = false)
    {
        $this->output->write(
            $this->linkConverter->convert($content),
            false,
            $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW
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
     * @param InputDefinition $definition
     * @return bool
     */
    private function describeInputDefinitionArguments(InputDefinition $definition): bool
    {
        if ($showArguments = count($definition->getArguments()) > 0) {
            $this->write('### Arguments');
            foreach ($definition->getArguments() as $argument) {
                $this->write("\n\n");
                $this->write($this->argumentDescriber->describe($argument));
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
                $this->write($this->optionDescriber->describe($option));
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
