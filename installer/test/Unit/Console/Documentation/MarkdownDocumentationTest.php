<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\Console\Documentation;

use Cocotte\Console\Documentation\MarkdownDocumentation;
use Cocotte\Test\Collaborator\Console\CommandInterfaceDouble;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\BufferedOutput;

final class MarkdownDocumentationTest extends TestCase
{
    /**
     * @var BufferedOutput
     */
    private $bufferedOutput;

    public function setUp()
    {
        $this->bufferedOutput = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true);
    }

    /**
     * @dataProvider argumentsProvider
     */
    public function testArguments(Command $command, string $expected)
    {
        $this->assertApplicationWithCommand($expected, $command);
    }

    public function argumentsProvider(): array
    {
        $providers = [
            'input_argument_0' => new InputArgument('argument_name', InputArgument::REQUIRED),
            'input_argument_1' => new InputArgument('argument_name', InputArgument::IS_ARRAY, 'argument description'),
            'input_argument_2' => new InputArgument('argument_name',
                InputArgument::OPTIONAL,
                'argument description',
                'default_value'),
            'input_argument_3' => new InputArgument('argument_name',
                InputArgument::REQUIRED,
                "multiline\nargument description"),
            'input_argument_4' => new InputArgument('argument_name',
                InputArgument::OPTIONAL,
                'argument description',
                /** @lang text */
                '<comment>style</>'),
            'input_argument_5' => new InputArgument('argument_name',
                InputArgument::OPTIONAL,
                'argument description',
                INF),
        ];

        $data = [];
        foreach ($providers as $filename => $inputArgument) {

            $expected = file_get_contents(__DIR__."/expected/${filename}.md");
            $command = CommandInterfaceDouble::create($this)->singleArgumentCommandStub($inputArgument);

            $data[] = array(
                $command,
                $expected,
            );
        }

        return $data;
    }

    private function assertApplicationWithCommand(string $expected, Command $command): void
    {
        $application = new Application();
        $application->add($command);
        $markdownDocumentation = new MarkdownDocumentation($this->bufferedOutput);

        $markdownDocumentation->document($application);
        $this->assertEquals(
            trim($expected),
            trim($this->bufferedOutput->fetch())
        );
    }
}

