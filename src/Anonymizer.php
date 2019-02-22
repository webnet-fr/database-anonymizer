<?php

namespace WebnetFr\DatabaseAnonymizer;

use Doctrine\DBAL\Driver\Connection;
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
     * @param Connection $connection
     * @param TargetTable[] $targets
     */
    public function anonymize(Connection $connection, array $targets)
    {
        foreach ($targets as $targetTable) {
            $allFieldNames = $targetTable->getAllFieldNames();
            $pk = $targetTable->getPrimaryKey();

            // Select all rows form current table:
            // SELECT <all target fields> FROM <target table>
            $fetchRowsSQL = sprintf('SELECT %s FROM `%s`', join(',', $allFieldNames), $targetTable->getName());
            $fetchRowsStmt = $connection->prepare($fetchRowsSQL);
            $fetchRowsStmt->execute();

            $updateFields = [];
            foreach ($targetTable->getTargetFields() as $targetField) {
                // <field_name>=:<field_name>
                $updateFields[] = '`'.$targetField->getName().'`=:'.$targetField->getName();
            }

            // UPDATE <table name> SET [<field_name=:field_name>] WHERE <pk>=:<pk>
            $updateSQL = sprintf('UPDATE `%s` SET %s WHERE `%s`=:%s', $targetTable->getName(), join(',', $updateFields), $pk, $pk);
            $updateStmt = $connection->prepare($updateSQL);

            // Anonymize all rows in current target table.
            while ($row = $fetchRowsStmt->fetch()) {
                // set primary key for row to update
                $updateStmt->bindValue($pk, $row[$pk]);

                // Anonymize all target fields in current row.
                foreach ($targetTable->getTargetFields() as $targetField) {
                    $anonValue = $targetField->generate();

                    if (!is_null($anonValue) && !is_string($anonValue)) {
                        throw new InvalidAnonymousValueException('Generated value must be null or string');
                    }

                    // Set anonymized value.
                    $updateStmt->bindValue($targetField->getName(), $anonValue);
                }

                // Update row.
                $updateStmt->execute();
            }
        }
    }
}
