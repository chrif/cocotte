<?php

declare(strict_types=1);

namespace Chrif\Cocotte;

use Chrif\Cocotte\Configuration\ApiToken;
use Chrif\Cocotte\Configuration\App\AppHostCollection;
use Chrif\Cocotte\Configuration\App\AppName;
use Chrif\Cocotte\Configuration\App\AppValues;
use Chrif\Cocotte\Configuration\Droplet\DropletIp;
use Chrif\Cocotte\Configuration\Droplet\DropletName;
use Chrif\Cocotte\Configuration\Droplet\DropletValues;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class CocotteConfiguration
{

    /**
     * @var array
     */
    private $value;

    /**
     * @param array $value
     */
    public function __construct(array $value)
    {
        $processor = new Processor();
        $this->value = $processor->process(
            $this->treeBuilder()->buildTree(),
            $value
        );
    }

    public static function fromResource(string $resource): self
    {
        return new self(Yaml::parse(file_get_contents($resource)));
    }

    /**
     * @codeCoverageIgnore
     */
    public static function fixture(): self
    {
        return new self(
            [
                'cocotte' => [
                    'api_token' => uniqid(ApiToken::API_TOKEN),
                    'droplet' => [
                        'name' => uniqid(DropletName::NAME),
                        'ip' => uniqid(DropletIp::IP),
                    ],
                    'app' => [
                        'name' => uniqid(AppName::NAME),
                        'hosts' => uniqid(AppHostCollection::HOSTS).'.org',
                    ],
                ],
            ]
        );
    }

    public function value(): array
    {
        return $this->value;
    }

    private function treeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cocotte');

        // @formatter:off
        $rootNode
            ->children()
                ->scalarNode(ApiToken::API_TOKEN)->defaultValue(getenv('DIGITAL_OCEAN_API_TOKEN'))->cannotBeEmpty()->end()
                ->arrayNode(DropletValues::DROPLET)
                    ->isRequired()
                    ->children()
                        ->scalarNode(DropletName::NAME)->defaultValue(getenv('MACHINE_NAME'))->cannotBeEmpty()->end()
                        ->scalarNode(DropletIp::IP)->defaultValue(getenv('MACHINE_IP'))->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode(AppValues::APP)
                    ->children()
                        ->scalarNode(AppName::NAME)->defaultValue(getenv('APP_NAME'))->cannotBeEmpty()->end()
                        ->scalarNode(AppHostCollection::HOSTS)->defaultValue(getenv('APP_HOSTS'))->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end();
        // @formatter:on

        return $treeBuilder;
    }
}