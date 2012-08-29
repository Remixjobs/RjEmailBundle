<?php

namespace Rj\EmailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('rj_email');

        $rootNode->children()
            ->scalarNode('custom_loader')->defaultValue(true)->end()
            ->scalarNode('default_locale')->defaultValue('en')->end()
            ->arrayNode('locales')
                ->isRequired()
                ->prototype('scalar')->end()
            ;

        $this->addEmailsSection($rootNode);
        return $treeBuilder;
    }

    private function addEmailsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('emails')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('confirmation')->defaultValue('confirmation')->end()
                        ->scalarNode('resetting')->defaultValue('confirmation')->end()
                    ->end()
                ->end()
            ->end();
    }
}
