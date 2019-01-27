<?php

namespace WebnetFr\DatabaseAnonymizer;

use Doctrine\DBAL\Driver\Connection;
use WebnetFr\DatabaseAnonymizer\Exception\InvalidAnonymousValueException;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class Anonymizer
{
    /**
     * @param Connection $connection
     * @param TargetTable[] $targets
     */
    public function anonymize(Connection $connection, array $targets)
    {
        foreach ($targets as $targetTable) {
            $allFieldNames = $targetTable->getAllFieldNames();
            $pk = $targetTable->getPrimaryKey();

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

            while ($row = $fetchRowsStmt->fetch()) {
                // set primary key for row to update
                $updateStmt->bindValue($pk, $row[$pk]);

                foreach ($targetTable->getTargetFields() as $targetField) {
                    $anonValue = $targetField->generate();

                    if (!is_null($anonValue) && !is_string($anonValue)) {
                        throw new InvalidAnonymousValueException('Generated value must be null or string');
                    }

                    // set anonymized value
                    $updateStmt->bindValue($targetField->getName(), $anonValue);
                }

                // update row
                $updateStmt->execute();
            }
        }
    }
}
