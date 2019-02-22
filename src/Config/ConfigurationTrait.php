<?php

namespace WebnetFr\DatabaseAnonymizer\Config;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
trait ConfigurationTrait
{
    /**
     * @param ArrayNodeDefinition $node
     *
     * @author Vlad Riabchenko <vriabchenko@webnet.fr>
     */
    public function configureAnonymizer(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('defaults')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('tables')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('primary_key')->end()
                            ->arrayNode('fields')
                                ->arrayPrototype()
                                    ->scalarPrototype()->end()
                                ->end()
                            ->end() // fields
                        ->end()
                    ->end()
                ->end() // tables
            ->end()
            ->beforeNormalization()
            // Pass default table configuration to field configuration.
            // defaults:
            //        locale: fr_FR
            //    tables:
            //        user:
            //            fields:
            //                name:
            //                    generator: first_name
            //                    # locale fr_FR will be available here.
            //                lastname:
            //                    generator: first_name
            //                    locale: en_EN
            //                    # locale en_EN overwrites default value fr_FR.
            ->ifTrue(static function ($v) {
                return is_array($v) && array_key_exists('defaults', $v) && is_array($v['defaults']);
            })
            ->then(static function ($c) {
                foreach ($c['tables'] as &$tableConfig) {
                    foreach ($tableConfig['fields'] as &$fieldConfig) {
                        foreach ($c['defaults'] as $defaultKey => $defaultValue) {
                            if (!array_key_exists($defaultKey, $fieldConfig)) {
                                $fieldConfig[$defaultKey] = $defaultValue;
                            }
                        }
                    }
                }

                return $c;
            })
            ->end()
        ;
    }
}
