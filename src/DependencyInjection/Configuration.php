<?php
/*
 * The MIT License
 *
 * Copyright 2017 Rob Treacy <robert.treacy@thesalegroup.co.uk>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace TheSaleGroup\RestormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Description of Configuration
 *
 * @author Rob Treacy <robert.treacy@thesalegroup.co.uk>
 */
class Configuration implements ConfigurationInterface
{
    
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder;
        
        $treeBuilder->root('thesalegroup_restorm')
            ->isRequired()
            ->children()
                ->arrayNode('connections')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('base_uri')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode('pagination_parameters')
                                ->children()
                                    ->scalarNode('page_param')->end()
                                    ->scalarNode('per_page_param')->end()
                                ->end()
                            ->end()
                            ->scalarNode('filter_mode')
                                ->defaultValue('query')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('transformers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('string')
                            ->defaultValue(\TheSaleGroup\Restorm\Normalizer\Transformer\TextTransformer::class)
                        ->end()
                        ->scalarNode('integer')
                            ->defaultValue(\TheSaleGroup\Restorm\Normalizer\Transformer\IntegerTransformer::class)
                        ->end()
                        ->scalarNode('float')
                            ->defaultValue(\TheSaleGroup\Restorm\Normalizer\Transformer\FloatTransformer::class)
                        ->end()
                        ->scalarNode('boolean')
                            ->defaultValue(\TheSaleGroup\Restorm\Normalizer\Transformer\BooleanTransformer::class)
                        ->end()
                        ->scalarNode('datetime')
                            ->defaultValue(\TheSaleGroup\Restorm\Normalizer\Transformer\DateTimeTransformer::class)
                        ->end()
                        ->scalarNode('entity')
                            ->defaultValue(\TheSaleGroup\Restorm\Normalizer\Transformer\EntityTransformer::class)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('entity_mappings')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('connection')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('repository_class')
                                ->defaultValue(\TheSaleGroup\Restorm\EntityRepository::class)
                            ->end()
                            ->arrayNode('paths')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->children()
                                    ->scalarNode('list')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('get')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('put')->end()
                                    ->scalarNode('patch')->end()
                                    ->scalarNode('post')->end()
                                    ->scalarNode('delete')->end()
                                ->end()
                            ->end()
                            ->arrayNode('properties')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->booleanNode('identifier')->end()
                                    ->scalarNode('type')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('map_from')->end()
                                    ->booleanNode('read_only')->end()
                                    ->booleanNode('dynamic')->end()
                                    ->booleanNode('multiple')->end()
                                    ->scalarNode('entity')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
