<?php declare(strict_types=1);

namespace Chrif\Cocotte\Command;

use Chrif\Cocotte\DigitalOcean\ApiToken;
use Chrif\Cocotte\DigitalOcean\ApiTokenInteraction;
use Chrif\Cocotte\DigitalOcean\HostnameCollection;
use Chrif\Cocotte\DigitalOcean\NetworkingConfigurator;
use Chrif\Cocotte\Environment\LazyEnvironment;
use Chrif\Cocotte\Environment\LazyEnvironmentLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class NetworkingCommand extends Command implements LazyEnvironment
{
    /**
     * @var NetworkingConfigurator
     */
    private $networkingConfigurator;

    /**
     * @var LazyEnvironmentLoader
     */
    private $lazyEnvironmentLoader;

    /**
     * @var ApiTokenInteraction
     */
    private $apiTokenInteraction;

    public function __construct(
        NetworkingConfigurator $networkingConfigurator,
        LazyEnvironmentLoader $lazyEnvironmentLoader,
        ApiTokenInteraction $apiTokenInteraction
    ) {
        $this->networkingConfigurator = $networkingConfigurator;
        $this->lazyEnvironmentLoader = $lazyEnvironmentLoader;
        $this->apiTokenInteraction = $apiTokenInteraction;
        parent::__construct();
    }

    public function isHidden()
    {
        return !getenv('SHOW_HIDDEN_COMMANDS');
    }

    public function requires(): array
    {
        return [
            ApiToken::class,
        ];
    }

    protected function configure()
    {
        $this
            ->setName('networking')
            ->setDescription('Configure networking of Digital Ocean')
            ->addArgument('hostnames', InputArgument::REQUIRED, 'Comma-separated list of hostnames')
            ->addOption('remove', null, InputOption::VALUE_NONE, 'Remove networking for hostnames')
            ->getDefinition()->addOptions(
                [
                    $this->apiTokenInteraction->option(),
                ]
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->lazyEnvironmentLoader->load($this, $input);
        $this->networkingConfigurator->configure(
            HostnameCollection::fromString($input->getArgument('hostnames')),
            $input->getOption('remove')
        );
    }

}