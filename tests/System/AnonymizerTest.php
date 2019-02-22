<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\System;

use Faker\Factory as FakerFactory;
use Faker\Provider\DateTime as FakerProviderDateTime;
use Faker\Provider\Person;
use Faker\Provider\PhoneNumber as FakerProviderPhoneNumber;
use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\Anonymizer;
use WebnetFr\DatabaseAnonymizer\Generator\DateTime;
use WebnetFr\DatabaseAnonymizer\Generator\FirstName;
use WebnetFr\DatabaseAnonymizer\Generator\LastName;
use WebnetFr\DatabaseAnonymizer\Generator\PhoneNumber;
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
    protected function setUp()
    {
        preg_match('/^(.*)\/([^\/]+)$/', getenv('db_url'), $matches);
        $url = $matches[1];
        $name = $matches[2];

        $this->regenerateUsersOrders($url, $name);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testAnonymizeUserTable()
    {
        $faker = FakerFactory::create();
        $targetFields[] = new TargetField('firstname', new FirstName(new Person($faker)));
        $targetFields[] = new TargetField('lastname', new LastName(new Person($faker)));
        $targetFields[] = new TargetField('birthdate', new DateTime(new FakerProviderDateTime($faker), ['format' => 'Y-m-d']));
        $targetFields[] = new TargetField('phone', new PhoneNumber(new FakerProviderPhoneNumber($faker)));
        $targets[] = new TargetTable('user', 'id', $targetFields);

        $connection = $this->getConnection(getenv('db_url'));
        $anonymizer = new Anonymizer();
        $anonymizer->anonymize($connection, $targets);

        $selectStmt = $connection->prepare('SELECT `firstname`, `lastname`, `birthdate`, `phone` FROM `user`');
        $selectStmt->execute();
        while ($row = $selectStmt->fetch()) {
            $this->assertTrue(is_string($row['firstname']));
            $this->assertTrue(is_string($row['lastname']));
            $this->assertTrue(is_string($row['birthdate']));
            $this->assertTrue(is_string($row['phone']));
        }
    }
}
