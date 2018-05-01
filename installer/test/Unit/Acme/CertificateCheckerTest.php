<?php

namespace Cocotte\Test\Unit\Acme;

use Cocotte\Acme\CertificateChecker;
use Cocotte\Test\Double\Console\TestStyle;
use Cocotte\Test\Double\Shell\ProcessRunnerMother;
use Cocotte\Test\PHPUnit\Constraint\ParameterGrabber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class CertificateCheckerTest extends TestCase
{

    const IP = '127.0.0.1';

    const LOCALHOST = 'localhost';

    public function setUp()
    {
        self::assertSame(self::IP, gethostbyname(self::LOCALHOST));
    }

    public function test_it_should_perform_the_check_when_ip_matches_expected()
    {
        $checker = new CertificateChecker(
            $processRunner = ProcessRunnerMother::create($this)->mock(),
            $style = new TestStyle()
        );

        $processRunner
            ->expects(self::once())
            ->method('mustRun')
            ->with($processGrabber = new ParameterGrabber());

        $checker->check(self::LOCALHOST, self::IP);

        /** @var Process $process */
        $process = $processGrabber->value();

        self::assertSame(
            'if [ "${ACME_ENABLED:-true}" = true ]; then '.
            "check-certificate localhost 6; ".
            'else echo "Skipping SSL verification"; fi',
            trim($process->getCommandLine())
        );
    }
}
