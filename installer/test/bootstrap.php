<?php

require_once __DIR__.'/../vendor/autoload.php';

// necessary when running PHPUnit with PHPStorm because entrypoint is overridden
$process = \Symfony\Component\Process\Process::fromShellCommandline('sh /installer/bin/bootstrap-container');
$process->mustRun();
