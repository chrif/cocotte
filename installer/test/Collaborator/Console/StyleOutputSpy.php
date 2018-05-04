<?php declare(strict_types=1);

namespace Cocotte\Test\Collaborator\Console;

use Cocotte\Console\Style;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Question\Question;

final class StyleOutputSpy extends Output implements Style
{
    public $output = '';

    public function clear()
    {
        $this->output = '';
    }

    public function write($messages, $newline = false, $options = 0)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function writeln($messages, $options = 0)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function setVerbosity($level)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function getVerbosity()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function isQuiet()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function isVerbose()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function isVeryVerbose()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function isDebug()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function setDecorated($decorated)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function isDecorated()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function getFormatter()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function block($messages, $type = null, $style = null, $prefix = ' ', $padding = false, $escape = true)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function askQuestion(Question $question)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function comment($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function ok($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function help($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function complete($messages): void
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function pause()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function optionHelp(string $title, array $message): string
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function quittableQuestion($message): string
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function link(string $url): string
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function hostnameHelp(string $name, string $subdomain): array
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function verbose($messages): void
    {
        $this->doWrite($messages, true);
    }

    public function veryVerbose($messages): void
    {
        $this->doWrite($messages, true);
    }

    public function debug($messages): void
    {
        $this->doWrite($messages, true);
    }

    public function createProgressBar($max = 0)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function title($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function section($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function listing(array $elements)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function text($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function success($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function error($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function warning($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function note($message)
    {
        $this->doWrite($message, true);
    }

    public function caution($message)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function table(array $headers, array $rows)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function ask($question, $default = null, $validator = null)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function askHidden($question, $validator = null)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function confirm($question, $default = true)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function choice($question, array $choices, $default = null)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function newLine($count = 1)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function progressStart($max = 0)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function progressAdvance($step = 1)
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    public function progressFinish()
    {
        throw new \RuntimeException('Method '.__METHOD__.' not implemented yet.');
    }

    protected function doWrite($message, $newline = false)
    {
        $line = $newline ? "\n" : '';
        if (is_array($message)) {
            $message = implode($line, $message);
        }
        $this->output .= $message.($line);
    }

}
