<?php declare(strict_types=1);

namespace Cocotte\Console\Documentation;

use Cocotte\Console\StyledInputOption;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class OptionDescriber
{

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function describe(InputOption $option)
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
     * @param $description
     * @param $emphasis
     * @return string
     */
    private function formatOptionDescription($description, $emphasis): string
    {
        return ($description ? $emphasis.preg_replace('/\s*[\r\n]\s*/', "\n", $description)."\n\n" : '');
    }

}