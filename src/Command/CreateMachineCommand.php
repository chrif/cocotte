<?php

declare(strict_types=1);

namespace App\Command;

use App\Lib\MachineCreationScript;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateMachineCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:machine:create')
            ->setDescription('Generate machine creation script')
            ->setHelp(
                <<<EOF
This command allows you to configure the creation of a Docker machine on Digital Ocean.
It generates a script that will create the machine for you.
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);

        $script = new MachineCreationScript();
        $file = $script->create();

        $style->success('Command written to '.$file->getBasename());

        return 0;
    }
}
