<?php declare(strict_types=1);

namespace Cocotte\Console\Documentation;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

final class ArgumentDescriber
{

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function describe(InputArgument $argument)
    {
        $description = $this->removeDecoration($argument->getDescription());
        $default = $this->formatDefaultValue($argument);

        $this->write(
            '#### `'.$this->argumentName($argument)."`\n\n"
            .$this->formatArgumentDescription($description)
            .'* Is required: '.$this->argumentIsRequired($argument)."\n"
            .'* Is array: '.$this->argumentIsArray($argument)."\n"
            .'* Default: `'.str_replace("\n", '', var_export($default, true)).'`'
        );
    }

    private function removeDecoration(string $string): string
    {
        $f = new OutputFormatter();

        return $f->format($string);
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
     * @return mixed|string
     */
    private function formatDefaultValue(InputArgument $argument)
    {
        $default = $argument->getDefault();
        if (is_string($default)) {
            $default = $this->removeDecoration($argument->getDefault());
        }

        return $default;
    }
}