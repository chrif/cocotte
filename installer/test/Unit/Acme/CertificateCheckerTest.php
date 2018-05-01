<?php

namespace Cocotte\Test\Unit\Acme;

use Cocotte\Acme\CertificateChecker;
use Cocotte\Test\Double\Console\TestStyle;
use Cocotte\Test\Double\Shell\ProcessRunnerMother;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class CertificateCheckerTest extends TestCase
{

    const IP = '127.0.0.1';

    const LOCALHOST = 'localhost';

    public function setUp()
    {
    }

    public function test_it_performs_the_check_if_ip_matches_expected()
    {
        self::assertSame(self::IP, gethostbyname(self::LOCALHOST));

        $checker = new CertificateChecker(
            $processRunner = ProcessRunnerMother::create($this)->mock(),
            $style = new TestStyle()
        );

        $processRunner
            ->expects(self::once())
            ->method('mustRun')
            ->with(self::callback(function (Process $process) {
                self::assertSame(
                    'if [ "${ACME_ENABLED:-true}" = true ]; then '.
                    "check-certificate localhost 6; ".
                    'else echo "Skipping SSL verification"; fi',
                    $process->getCommandLine()
                );

                return true;
            }));

        $checker->check(self::LOCALHOST, self::IP);

        self::assertSame('', $style->output);
    }

    public function test_it_just_leaves_a_note_if_ip_does_not_match_expected()
    {
        $checker = new CertificateChecker(
            $processRunner = ProcessRunnerMother::create($this)->mock(),
            $style = new TestStyle()
        );

        $processRunner
            ->expects(self::never())
            ->method('mustRun');

        $checker->check(self::LOCALHOST, '0.0.0.0');

        self::assertSame(
            'Skipping SSL verification because localhost resolves to 127.0.0.1 instead of machine ip which is 0.0.0.0
You should wait for DNS to update or force localhost to 0.0.0.0 in your /etc/hosts file.',
            $style->output);
    }

}
