<?php

namespace WebnetFr\DatabaseAnonymizer;

use WebnetFr\DatabaseAnonymizer\Event\AnonymizerEvent;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Database anonymizer.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Anonymizer
{
    public function __construct(?EventDispatcherInterface $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Anonymize entire database based on target tables.
     *
     * @param Connection $connection
     * @param TargetTable[] $targets
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function anonymize(Connection $connection, array $targets)
    {
        foreach ($targets as $targetTable) {
            if ($targetTable->isTruncate()) {
                $dbPlatform = $connection->getDatabasePlatform();
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $truncateQuery = $dbPlatform->getTruncateTableSql($targetTable->getName());
                $connection->executeUpdate($truncateQuery);
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
            } else {
                // Anonymize all rows in current target table.
                $values = [];
                foreach ($targetTable->getTargetFields() as $targetField) {
                    if (!isset($this->fakerCache[$targetField->getName()])) {
                        $this->fakerCache[$targetField->getName()] = $targetField->generate();
                    }

                    // Set anonymized value.
                    $values[$targetField->getName()] = $this->fakerCache[$targetField->getName()];
                }

                $connection->update($targetTable->getName(), $values, [true => true]);

                if (null !== $this->dispatcher) {
                    $this->dispatcher->dispatch(new AnonymizerEvent($targetTable->getName(), $values));
                }
            }
        }
    }
}
