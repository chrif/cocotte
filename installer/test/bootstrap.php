<?php

require_once __DIR__.'/../vendor/autoload.php';

// necessary when running PHPUnit with PHPStorm because entrypoint is overridden
$process = new \Symfony\Component\Process\Process('php /installer/bin/bootstrap-container');
$process->mustRun();
