<?php

namespace WebnetFr\DatabaseAnonymizer;

use Doctrine\DBAL\Connection;
use WebnetFr\DatabaseAnonymizer\Exception\InvalidAnonymousValueException;

/**
 * Database anonymizer.
 *
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Anonymizer
{
    /**
     * Anonymize entire database based on target tables.
     *
     * @param Connection    $connection
     * @param TargetTable[] $targets
     */
    public function anonymize(Connection $connection, array $targets)
    {
        foreach ($targets as $targetTable) {
            $allFieldNames = $targetTable->getAllFieldNames();
            $pk = $targetTable->getPrimaryKey();

            // Select all rows form current table:
            // SELECT <all target fields> FROM <target table>
            $fetchRowsSQL = $connection->createQueryBuilder()
                ->select(implode(',', $allFieldNames))
                ->from($targetTable->getName())
                ->getSQL()
            ;
            $fetchRowsStmt = $connection->prepare($fetchRowsSQL);
            $fetchRowsStmt->execute();

            // Anonymize all rows in current target table.
            while ($row = $fetchRowsStmt->fetch()) {
                $values = [];
                // Anonymize all target fields in current row.
                foreach ($targetTable->getTargetFields() as $targetField) {
                    $anonValue = $targetField->generate();

                    if (null !== $anonValue && !\is_string($anonValue)) {
                        throw new InvalidAnonymousValueException('Generated value must be null or string');
                    }

                    // Set anonymized value.
                    $values[$targetField->getName()] = $anonValue;
                }

                $pkValues = [];
                foreach ($pk as $pkField) {
                    $pkValues[$pkField] = $row[$pkField];
                }

                $connection->update($targetTable->getName(), $values, $pkValues);
            }
        }
    }
}
