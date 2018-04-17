<?php declare(strict_types=1);

namespace Chrif\Cocotte\Environment;

use Assert\Assertion;
use Chrif\Cocotte\Console\Style;
use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LazyEnvironmentLoader
{
    /**
     * @var LazyEnvironmentValue|LazyLoadingInterface[]
     */
    private $values = [];

    /**
     * @var Style
     */
    private $style;

    public function __construct(Style $style)
    {
        $this->style = $style;
    }

    public function addValue(LazyEnvironmentValue $value)
    {
        $this->values[] = $value;
    }

    public function load(LazyEnvironment $environment, InputInterface $input)
    {
        foreach ($environment->requires() as $require) {
            $lazyValue = $this->getLazyValue($require);
            if ($lazyValue instanceof LazyOptionExportValue) {
                $this->exportOption($input, $lazyValue);
            }
            $this->initializeProxy($lazyValue);
        }
        $this->style->writeln("Lazy loaded env:\n".print_r(getenv(), true), OutputInterface::VERBOSITY_VERBOSE);
    }

    private function getLazyValue(string $className): LazyLoadingInterface
    {
        foreach ($this->values as $value) {
            if ($value instanceof $className) {
                return $value;
            }
        }
        throw new \Exception("Could not find '$className'");
    }

    private function initializeProxy(LazyLoadingInterface $lazyValue)
    {
        if ($lazyValue->isProxyInitialized()) {
            throw new \Exception(
                sprintf(
                    "Managing a lazy environment value ".
                    "'%s' which is already initialized.",
                    $lazyValue
                )
            );
        }
        $lazyValue->initializeProxy();
        if ($lazyValue instanceof LazyLoadAware) {
            $lazyValue->onLazyLoad();
        }
    }

    private function exportOption(InputInterface $input, LazyOptionExportValue $optionExportValue): void
    {
        $name = $optionExportValue::optionName();
        Assertion::true($input->hasOption($name), sprintf("input does not have an option named '%s'", $name));
        $value = $input->getOption($name);
        if (null !== $value) {
            $optionExportValue::toEnv($value);
        }
    }

}