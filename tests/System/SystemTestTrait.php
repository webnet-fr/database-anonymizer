<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\System;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Schema\Schema;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
trait SystemTestTrait
{
    /**
     * @param string $url
     *
     * @return Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getConnection(string $url): Connection
    {
        $params = ['url' => $url];
        $config = new Configuration();

        return DriverManager::getConnection($params, $config);
    }

    /**
     * @param string $url
     * @param string $name
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function regenerateUsersOrders($url, $name): void
    {
        $connection = $this->getConnection($url);
        $schemaManager = $connection->getSchemaManager();
        $schema = new Schema();

        try {
            $schemaManager->dropDatabase($name);
        } catch (DriverException $e) {
            // If tardet database doesn't exist.
        }

        $schemaManager->createDatabase($name);
        $connection->query('USE '.$name);

        $user = $schema->createTable('user');
        $user->addColumn('id', 'integer', ['id' => true, 'unsigned' => true, 'unique']);
        $user->addColumn('email', 'string', ['length' => 256, 'notnull' => false]);
        $user->addColumn('firstname', 'string', ['length' => 256, 'notnull' => false]);
        $user->addColumn('lastname', 'string', ['length' => 256, 'notnull' => false]);
        $user->addColumn('birthdate', 'date', ['notnull' => false]);
        $user->addColumn('phone', 'string', ['length' => 20, 'notnull' => false]);
        $user->addColumn('password', 'string', ['length' => 64, 'notnull' => false]);
        $user->setPrimaryKey(['id']);
        $schemaManager->createTable($user);

        $order = $schema->createTable('order');
        $order->addColumn('id', 'integer', ['unsigned' => true]);
        $order->addColumn('address', 'string', ['length' => 256, 'notnull' => false]);
        $order->addColumn('street_address', 'string', ['length' => 64, 'notnull' => false]);
        $order->addColumn('zip_code', 'string', ['length' => 10, 'notnull' => false]);
        $order->addColumn('city', 'string', ['length' => 64, 'notnull' => false]);
        $order->addColumn('country', 'string', ['length' => 64, 'notnull' => false]);
        $order->addColumn('comment', 'text', ['notnull' => false]);
        $order->addColumn('created_at', 'datetime', ['notnull' => false]);
        $order->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $order->setPrimaryKey(['id']);
        $order->addForeignKeyConstraint($user, ['user_id'], ['id']);
        $schemaManager->createTable($order);

        $userData = [];
        foreach (range(1, 10) as $i) {
            $userData[] = "($i)";
        }
        $userDataStmt = $connection->prepare('INSERT INTO `user` (`id`) VALUES '.join(',', $userData));
        $userDataStmt->execute();

        $orderData = [];
        foreach (range(1, 20) as $i) {
            $orderData[] = sprintf('(%d, %d)', $i, mt_rand(1, 10));
        }
        $orderDataStmt = $connection->prepare('INSERT INTO `order` (`id`, `user_id`) VALUES '.join(',', $orderData).';');
        $orderDataStmt->execute();

        $connection->close();
    }
}
