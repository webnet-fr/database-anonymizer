<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\System;

use Faker\Factory as FakerFactory;
use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Anonymizer;
use WebnetFr\DatabaseAnonymizer\Generator\FakerGenerator;
use WebnetFr\DatabaseAnonymizer\TargetField;
use WebnetFr\DatabaseAnonymizer\TargetTable;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AnonymizerTest extends TestCase
{
    use SystemTestTrait;

    /**
     * @inheritdoc
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp(): void
    {
        $this->regenerateUsersOrders();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testAnonymizeUserTable()
    {
        $faker = FakerFactory::create();
        $targetFields[] = new TargetField('firstname', new FakerGenerator($faker, 'firstName'));
        $targetFields[] = new TargetField('lastname', new FakerGenerator($faker, 'lastName'));
        $targetFields[] = new TargetField('birthdate', new FakerGenerator($faker, 'dateTime', [], ['date_format' => 'Y-m-d H:i:s']));
        $targetFields[] = new TargetField('phone', new FakerGenerator($faker, 'e164PhoneNumber'));
        $targets[] = new TargetTable('users', ['id'], $targetFields, false);

        $connection = $this->getConnection();
        $anonymizer = new Anonymizer();
        $anonymizer->anonymize($connection, $targets);

        $selectSQL = $connection->createQueryBuilder()
            ->select('u.firstname, u.lastname, u.birthdate, u.phone')
            ->from('users', 'u')
            ->getSQL();

        $selectStmt = $connection->prepare($selectSQL);
        $result = $selectStmt->execute();
        while ($row = $result->fetchAssociative()) {
            $this->assertTrue(is_string($row['firstname']));
            $this->assertTrue(is_string($row['lastname']));
            $this->assertTrue(is_string($row['birthdate']));
            $this->assertTrue(is_string($row['phone']));
        }
    }

    public function testTruncate()
    {
        $targets = [
            new TargetTable('users', [], [], true),
            new TargetTable('orders', [], [], true),
            new TargetTable('productivity', [], [], true),
        ];

        $connection = $this->getConnection();
        $anonymizer = new Anonymizer();
        $anonymizer->anonymize($connection, $targets);

        $selectSQL = $connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('users', 'u')
            ->getSQL();
        $selectStmt = $connection->prepare($selectSQL);
        $users = $selectStmt->execute();
        $result = $users->fetchAssociative();
        $this->assertEquals(0, current($result));

        $selectSQL = $connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('orders', 'o')
            ->getSQL();
        $selectStmt = $connection->prepare($selectSQL);
        $orders = $selectStmt->execute();
        $result = $orders->fetchAssociative();
        $this->assertEquals(0, current($result));

        $selectSQL = $connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('productivity', 'p')
            ->getSQL();
        $selectStmt = $connection->prepare($selectSQL);
        $productivity = $selectStmt->execute();
        $result = $productivity->fetchAssociative();
        $this->assertEquals(0, current($result));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->getConnection()->close();
    }
}
