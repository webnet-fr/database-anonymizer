<?php

namespace WebnetFr\DatabaseAnonymizer\Config;

use WebnetFr\DatabaseAnonymizer\GeneratorFactory\GeneratorFactoryInterface;
use WebnetFr\DatabaseAnonymizer\TargetField;
use WebnetFr\DatabaseAnonymizer\TargetTable;

/**
 * Creates targets based on configuration.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class TargetFactory
{
    /**
     * Creates generator based on configuration.
     *
     * @var GeneratorFactoryInterface
     */
    private $generatorFactory;

    /**
     * @param GeneratorFactoryInterface $generatorFactory
     */
    public function __construct(GeneratorFactoryInterface $generatorFactory)
    {
        $this->generatorFactory = $generatorFactory;
    }

    /**
     * Given configuration returns an array of @see TargetTable. 
     * 
     * @param array $config
     * [
     *     <table name> => [
     *         'primary_key' => <name of primary key field>,
     *         'fields' => [
     *             <field name> => <field config>,
     *             ...
     *         ]
     *     ],
     *     ...
     * ]
     *
     * @return TargetTable[]
     */
    public function createTargets(array $config): array
    {
        $targetTables = [];

        foreach ($config['tables'] as $tableName => $tableConfig) {
            $targetFields = [];

            foreach ($tableConfig['fields'] as $fieldName => $fieldConfig) {
                $generator = $this->generatorFactory->getGenerator($fieldConfig);
                $targetFields[] = new TargetField($fieldName, $generator);
            }

            $targetTables[] = new TargetTable($tableName, $tableConfig['primary_key'], $targetFields);
        }

        return $targetTables;
    }
}
