<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\Console;

use Cocotte\Console\CocotteStyle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class CocotteStyleTest extends TestCase
{

    public function setUp()
    {
        // same as vendor/symfony/console/Tests/Style/SymfonyStyleTest.php:32
        putenv('COLUMNS=121');
    }

    public function tearDown()
    {
        putenv('COLUMNS');
    }

    public function testPause()
    {
        $this->assertInteractiveMode(
            [
                "foo",
            ],
            function (CocotteStyle $style) {
                self::assertSame("foo", $style->pause());
            },
            function (string $display) {
                self::assertSame(
                    "\n Press ENTER to continue or press CTRL+D to quit:\n > \n",
                    $display
                );
            }
        );
    }

    private function normalizeDisplay(string $display): string
    {
        /**
         * strip container output line prefix:
         *
         * Sample:
         *
         * cmd_1          |
         * cmd_1          | cmd_1          |
         * cmd_1          | cmd_1          |
         * cmd_1          |
         *
         */
        // https://github.com/moby/moby/blob/master/daemon/names/names.go#L6
        $containerName = '[a-zA-Z0-9][a-zA-Z0-9_\.-]';
        $space = ' ';
        $display = preg_replace("/^({$containerName}{$space}{10}|{$space})+/", '', $display);

        return $display;
    }

    private function assertInteractiveMode(array $inputs, \Closure $runInteractive, \Closure $assertDisplay): void
    {
        $command = new Command('style');
        $command->setCode(
            function (InputInterface $input, OutputInterface $output) use ($runInteractive) {
                $style = new CocotteStyle($input, $output);
                $runInteractive($style);
            }
        );
        $tester = new CommandTester($command);
        $tester->setInputs($inputs);
        $tester->execute([], ['interactive' => true, 'decorated' => false]);
        $assertDisplay($this->normalizeDisplay($tester->getDisplay()));
    }

}
