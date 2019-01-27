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
     * @param bool $toDatabase
     *
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getConnection(bool $toDatabase = true)
    {
        $url = getenv('db_url');
        if (!$toDatabase) {
            // get server url without database
            $url = preg_replace('/\/[^\/]+$/', '', $url);
        }

        $config = new Configuration();
        $params = ['url' => $url];

        return DriverManager::getConnection($params, $config);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function regenerateDB()
    {
        $connection = $this->getConnection(false);
        $regenerateSQL = file_get_contents('tests/System/fixtures/regenerate_db.sql');

        $fetchRowsStmt = $connection->prepare($regenerateSQL);
        $fetchRowsStmt->execute();
        $connection->close();
    }
}
