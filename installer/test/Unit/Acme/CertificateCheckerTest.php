<?php

namespace Cocotte\Test\Unit\Acme;

use Cocotte\Acme\CertificateChecker;
use Cocotte\Test\Double\Console\StyleDouble;
use Cocotte\Test\Double\Shell\ProcessRunnerDouble;
use PHPUnit\Framework\TestCase;

class CertificateCheckerTest extends TestCase
{

    public function test_it_performs_the_check_if_ip_matches_expected()
    {
        self::assertSame('127.0.0.1', gethostbyname('localhost'));

        $checker = new CertificateChecker(
            ProcessRunnerDouble::create($this)->mustRunCommandMock(
                'if [ "${ACME_ENABLED:-true}" = true ]; then '.
                "check-certificate localhost 6; ".
                'else echo "Skipping SSL verification"; fi'
            ),
            $style = StyleDouble::create($this)->outputSpy()
        );

        $checker->check('localhost', '127.0.0.1');

        self::assertSame('', $style->output);
    }

    public function test_it_just_leaves_a_note_if_ip_does_not_match_expected()
    {
        $checker = new CertificateChecker(
            $processRunner = ProcessRunnerDouble::create($this)->mustNotRunMock(),
            $style = StyleDouble::create($this)->outputSpy()
        );

        $checker->check('localhost', '0.0.0.0');

        self::assertSame(
            'Skipping SSL verification because localhost resolves to 127.0.0.1 instead of machine ip which is 0.0.0.0
You should wait for DNS to update or force localhost to 0.0.0.0 in your /etc/hosts file.',
            $style->output);
    }

}
