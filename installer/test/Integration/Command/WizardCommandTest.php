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

        self::assertSame(
            $this->rightTrimAllLines($this->formatExpectedDisplay()),
            $this->rightTrimAllLines(OutputActual::get($this->container())->getDisplay())
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
