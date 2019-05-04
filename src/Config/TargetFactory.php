<?php

namespace WebnetFr\DatabaseAnonymizer\Config;

use Doctrine\DBAL\Connection;
use WebnetFr\DatabaseAnonymizer\Exception\UnknownPrimaryKeyException;
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
     * @var Connection
     */
    private $connection;

    /**
     * @param GeneratorFactoryInterface $generatorFactory
     */
    public function __construct(GeneratorFactoryInterface $generatorFactory)
    {
        $this->generatorFactory = $generatorFactory;
    }

    /**
     * Set connection.
     *
     * @param Connection $connection
     *
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Given configuration returns an array of @see TargetTable.
     *
     * @param array $config
     *                      [
     *                      <table name> => [
     *                      'primary_key' => <name of primary key field>,
     *                      'fields' => [
     *                      <field name> => <field config>,
     *                      ...
     *                      ]
     *                      ],
     *                      ...
     *                      ]
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

            $primaryKey = $tableConfig['primary_key'] ?? null;
            if (!$primaryKey) {
                if (!$this->connection) {
                    throw new UnknownPrimaryKeyException(sprintf("You must eigher set 'primary_key' on '%s' table or provide %s with Doctrine\\DBAL\\Connection instance via 'setConnection' method.", $tableName, self::class));
                }
                $schemaManager = $this->connection->getSchemaManager();
                $indexes = $schemaManager->listTableIndexes($tableName);
                foreach ($indexes as $index) {
                    /** @var \Doctrine\DBAL\Schema\Index $index */
                    if ($index->isPrimary()) {
                        $primaryKey = $index->getColumns();

                        break;
                    }
                }
            }

            $targetTables[] = new TargetTable($tableName, $primaryKey, $targetFields);
        }

        return $targetTables;
    }
}
