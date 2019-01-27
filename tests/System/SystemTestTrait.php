<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\System;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
trait SystemTestTrait
{
    /**
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getConnection()
    {
        $config = new Configuration();
        $params = ['url' => getenv('db_url')];

        return DriverManager::getConnection($params, $config);
    }

    /**
     * @param Connection $connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function regenerateDB(Connection $connection)
    {
        $regenerateSQL = file_get_contents('tests/System/fixtures/regenerate_db.sql');

        $fetchRowsStmt = $connection->prepare($regenerateSQL);
        $fetchRowsStmt->execute();
    }
}
