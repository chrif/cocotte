<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\Console\MarkdownDescriptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DocCommand extends Command
{

    public function isHidden()
    {
        return true;
    }

    protected function configure(): void
    {
        $this
            ->setName('doc');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $descriptor = new MarkdownDescriptor();
        $descriptor->describe($output, $this->getApplication());
    }

}
