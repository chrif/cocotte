<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\DigitalOcean;

use Cocotte\Console\CommandBeforeInitializeEvent;
use Cocotte\Console\OptionProviderRegistry;
use Cocotte\DigitalOcean\SkipDnsValidation;
use Cocotte\Test\Collaborator\Shell\FakeEnv;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

final class SkipDnsValidationTest extends TestCase
{

    public function test_it_does_not_put_env_if_option_does_not_exist()
    {
        $skipDnsValidation = new SkipDnsValidation(
            new OptionProviderRegistry(),
            $env = new FakeEnv()
        );
        $skipDnsValidation->onCommandBeforeInitialize(
            new CommandBeforeInitializeEvent(
                new ArgvInput(
                    ['foo.php'],
                    new InputDefinition([])
                )
            )
        );
        self::assertSame(null, $env->get(SkipDnsValidation::SKIP_DNS_VALIDATION));
    }

    public function test_it_does_not_put_env_if_option_is_not_set()
    {
        $skipDnsValidation = new SkipDnsValidation(
            new OptionProviderRegistry(),
            $env = new FakeEnv()
        );
        $skipDnsValidation->onCommandBeforeInitialize(
            new CommandBeforeInitializeEvent(
                new ArgvInput(
                    ['foo.php'],
                    new InputDefinition(
                        [
                            new InputOption(
                                'skip-dns-validation',
                                null,
                                InputOption::VALUE_NONE
                            ),
                        ]
                    )
                )
            )
        );
        self::assertSame(null, $env->get(SkipDnsValidation::SKIP_DNS_VALIDATION));
    }

    public function test_it_puts_env_if_value_is_set()
    {
        $skipDnsValidation = new SkipDnsValidation(
            new OptionProviderRegistry(),
            $env = new FakeEnv()
        );
        $skipDnsValidation->onCommandBeforeInitialize(
            new CommandBeforeInitializeEvent(
                new ArgvInput(
                    ['foo.php', '--skip-dns-validation'],
                    new InputDefinition(
                        [
                            new InputOption(
                                'skip-dns-validation',
                                null,
                                InputOption::VALUE_NONE
                            ),
                        ]
                    )
                )
            )
        );
        self::assertSame('1', $env->get(SkipDnsValidation::SKIP_DNS_VALIDATION));
    }

}
