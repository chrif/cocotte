<?php declare(strict_types=1);

namespace Cocotte\DigitalOcean;

use Cocotte\Console\CommandBeforeInitializeEvent;
use Cocotte\Console\CommandConfigureEvent;
use Cocotte\Console\CommandEventStore;
use Cocotte\Console\OptionProviderRegistry;
use Cocotte\Shell\Env;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SkipDnsValidation implements EventSubscriberInterface
{
    private const OPTION_NAME = 'skip-dns-validation';
    public const SKIP_DNS_VALIDATION = 'SKIP_DNS_VALIDATION';

    /**
     * @var OptionProviderRegistry
     */
    private $registry;
    /**
     * @var Env
     */
    private $env;

    public function __construct(OptionProviderRegistry $registry, Env $env)
    {
        $this->registry = $registry;
        $this->env = $env;
    }

    public static function getSubscribedEvents()
    {
        return [
            CommandEventStore::COMMAND_CONFIGURE => 'onCommandConfigure',
            CommandEventStore::COMMAND_BEFORE_INITIALIZE => 'onCommandBeforeInitialize',
        ];
    }

    public function onCommandConfigure(CommandConfigureEvent $event)
    {
        $command = $event->command();

        foreach ($command->optionProviders() as $className) {
            $optionProvider = $this->registry->providerByClassName($className);

            if ($optionProvider instanceof DnsValidated) {
                $event->inputDefinition()->addOption(
                    new InputOption(
                        self::OPTION_NAME,
                        null,
                        InputOption::VALUE_NONE,
                        "Cocotte uses a third-party library to validate that the name servers of your\n".
                        "domain point to Digital Ocean. If you are confident that your name servers\n".
                        "are correct, you can skip DNS validation."
                    )
                );
                break;
            }
        }
    }

    public function onCommandBeforeInitialize(CommandBeforeInitializeEvent $event)
    {
        $input = $event->input();
        $name = self::OPTION_NAME;

        if ($input->hasOption($name)) {
            $this->env->put(self::SKIP_DNS_VALIDATION, '1');
        }
    }

}