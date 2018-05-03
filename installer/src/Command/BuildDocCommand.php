<?php declare(strict_types=1);

namespace Cocotte\Command;

use Cocotte\Console\MarkdownDescriptor;
use Cocotte\DigitalOcean\ApiToken;
use Cocotte\Shell\Env;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BuildDocCommand extends Command
{
    /**
     * @var Env
     */
    private $env;

    public function isHidden()
    {
        return true;
    }

    protected function configure(): void
    {
        $this->setName('build-doc');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->env->get(ApiToken::DIGITAL_OCEAN_API_TOKEN)) {
            throw new \Exception("Environment is populated. This command needs to run on a bare environment.");
        }

        $descriptor = new MarkdownDescriptor();
        $descriptor->describe($output, $this->getApplication());
    }

}
