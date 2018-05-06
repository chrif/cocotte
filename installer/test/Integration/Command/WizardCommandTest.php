<?php declare(strict_types=1);

namespace Cocotte\Test\Integration\Command;

use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Template\Traefik\TraefikHostname;
use Cocotte\Template\Traefik\TraefikPassword;
use Cocotte\Template\Traefik\TraefikUsername;
use Cocotte\Test\ApplicationTestCase;
use Cocotte\Test\Collaborator\Command\WizardCommandActual;
use Cocotte\Test\Collaborator\Console\InputActual;
use Cocotte\Test\Collaborator\Console\OutputActual;
use Cocotte\Test\TestCompilerPass;
use Symfony\Component\Process\Process;

final class WizardCommandTest extends ApplicationTestCase
{
    public function setUp()
    {
        putenv('COLUMNS=121');
    }

    public function tearDown()
    {
        putenv('COLUMNS');
    }

    public function testExecute()
    {
        // prepare answers
        InputActual::get($this->container())->setInputs([
            "\r", // $this->style->pause();
            getenv(ApiToken::DIGITAL_OCEAN_API_TOKEN), // $this->ask(ApiToken::OPTION_NAME);
            "\r", // ApiTokenOptionProvider::onCorrectAnswer $this->style->pause();
            getenv(TraefikHostname::TRAEFIK_UI_HOSTNAME), // $this->ask(TraefikHostname::OPTION_NAME);
            "\r", // TraefikHostnameOptionProvider::onCorrectAnswer $this->style->pause();
            getenv(TraefikUsername::TRAEFIK_UI_USERNAME), // $this->ask(TraefikUsername::OPTION_NAME);
            getenv(TraefikPassword::TRAEFIK_UI_PASSWORD), // $this->ask(TraefikPassword::OPTION_NAME);
            "\r", // $this->style->pause();
        ]);

        // run
        WizardCommandActual::get($this->container())->service()->run(
            InputActual::get($this->container())->service(),
            OutputActual::get($this->container())->service());

        $display = OutputActual::get($this->container())->getDisplay();

        self::assertSame(
            $this->rightTrimAllLines($this->formatExpectedDisplay()),
            $this->rightTrimAllLines($display)
        );

        // parse command
        self::assertSame(1, preg_match('/Run this command:\s+(docker run[^;]+);/', $display, $matches));

        return $matches[1];
    }

    /**
     * @depends testExecute
     * @param string $command
     */
    public function testCommand(string $command)
    {
        // do not allocate a pseudo-TTY
        self::assertContains(' -it ', $command);
        $command = str_replace(' -it ', ' ', $command);
        $command .= " --dry-run";

        $process = new Process($command);
        $process->mustRun();
        self::assertContains(
            "Would have created a Docker machine named 'cocotte' on Digital Ocean.",
            $process->getOutput()
        );
    }

    /**
     * @return TestCompilerPass
     */
    protected function compilerPass(): TestCompilerPass
    {
        return new TestCompilerPass(false);
    }

    private function formatExpectedDisplay(): string
    {
        // substitute env variables
        $process = new Process('envsubst');
        $process->setInput(file_get_contents(__DIR__.'/wizard_expected_display.txt'));
        $process->mustRun();

        return $process->getOutput();
    }

    private function rightTrimAllLines(string $string): string
    {
        return implode("\n", array_map('rtrim', explode("\n", $string)));
    }

}
