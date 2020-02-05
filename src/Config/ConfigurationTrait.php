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
                            ->booleanNode('truncate')->defaultFalse()->end()
                            ->arrayNode('primary_key')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('fields')
                                ->arrayPrototype()
                                    ->variablePrototype()->end()
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
            //        seed: seed_key
            //    tables:
            //        user:
            //            fields:
            //                name:
            //                    generator: first_name
            //                    # locale "fr_FR" will be set here.
            //                    # seed "seed_key" will be set here.
            //                lastname:
            //                    generator: first_name
            //                    locale: en_EN
            //                    seed: lastname_seed_key
            //                    # locale "en_EN" overwrites default value fr_FR.
            //                    # seed "lastname_seed_key" overwrites default value "seed_key".
            ->ifTrue(static function ($v) {
                return \is_array($v) && \array_key_exists('defaults', $v) && \is_array($v['defaults']);
            })
            ->then(static function ($c) {
                if (isset($c['tables'])) {
                    foreach ($c['tables'] as &$tableConfig) {
                        if ($tableConfig['fields']) {
                            foreach ($tableConfig['fields'] as &$fieldConfig) {
                                if (isset($c['defaults'])) {
                                    foreach ($c['defaults'] as $defaultKey => $defaultValue) {
                                        if (!\array_key_exists($defaultKey, $fieldConfig)) {
                                            $fieldConfig[$defaultKey] = $defaultValue;
                                        }
                                    }
                                }
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
