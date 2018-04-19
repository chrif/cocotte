<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class HelperFactory
{
    public function processHelper(): ProcessHelper
    {
        $helper = new ProcessHelper();
        $helper->setHelperSet(new HelperSet([new DebugFormatterHelper()]));

        return $helper;
    }
}
