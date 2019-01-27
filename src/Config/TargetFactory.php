<?php

namespace WebnetFr\DatabaseAnonymizer\Config;

use WebnetFr\DatabaseAnonymizer\GeneratorFactory\GeneratorFactoryInterface;
use WebnetFr\DatabaseAnonymizer\TargetField;
use WebnetFr\DatabaseAnonymizer\TargetTable;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class TargetFactory
{
    /**
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
     * @param array $config
     *
     * @return TargetTable[]
     */
    public function createTargets(array $config)
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
