<?php declare(strict_types=1);

namespace Chrif\Cocotte\Console;

final class OptionProviderRegistry
{
    /**
     * @var OptionProvider[]
     */
    private $providersByOptionName;
    /**
     * @var OptionProvider[]
     */
    private $providersByClassName;

    public function registerProvider(OptionProvider $optionProvider)
    {
        $this->registerClassName($optionProvider);
        $this->registerOptionName($optionProvider);
    }

    public function providerByOptionName(string $optionName): OptionProvider
    {
        if (!isset($this->providersByOptionName[$optionName])) {
            throw new \Exception(sprintf("Option provider '{$optionName}' is not registered"));
        }

        return $this->providersByOptionName[$optionName];
    }

    public function providerByClassName(string $className): OptionProvider
    {
        if (!is_subclass_of($className, OptionProvider::class)) {
            throw new \Exception(sprintf("Option provider '%s' does not implement '%s'",
                    $className,
                    OptionProvider::class)
            );
        }

        if (!isset($this->providersByClassName[$className])) {
            throw new \Exception(sprintf("Option provider '{$className}' is not registered"));
        }

        return $this->providersByClassName[$className];
    }

    private function registerOptionName(OptionProvider $provider)
    {
        $name = $provider->optionName();

        if (isset($this->providersByOptionName[$name])) {
            throw new \Exception($name." cannot be registered because this name is already ".
                "registered with provider ".get_class($this->providersByOptionName[$name]));
        }

        $this->providersByOptionName[$name] = $provider;
    }

    private function registerClassName(OptionProvider $provider)
    {
        $class = get_class($provider);

        if (isset($this->providersByClassName[$class])) {
            throw new \Exception($class." cannot be registered because this name is already ".
                "registered with provider ".get_class($this->providersByClassName[$class]));
        }

        $this->providersByClassName[$class] = $provider;
    }

}
