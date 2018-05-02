<?php declare(strict_types=1);

namespace Cocotte\Console;

use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;

final class HelperFactory
{
    public function processHelper(): ProcessHelper
    {
        $helper = new ProcessHelper();
        $helper->setHelperSet(new HelperSet([new DebugFormatterHelper()]));

        return $helper;
    }
}
