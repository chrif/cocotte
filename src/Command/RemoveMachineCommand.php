<?php

declare(strict_types=1);

namespace App\Command;

use App\Lib\MachineCreationScript;
use App\Lib\MachineRemovalScript;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RemoveMachineCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:machine:remove')
            ->setDescription('Generate machine removal script')
            ->setHelp(
                <<<EOF
This command generates a script that will destroy the machine for you.
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $script = new MachineRemovalScript();
        $file = $script->create();

        $style->success('Command written to '.$file->getBasename());

        return 0;
    }
}
