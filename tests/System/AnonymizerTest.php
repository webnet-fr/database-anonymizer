<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\System;

use Faker\Factory as FakerFactory;
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
     */
    protected function setUp()
    {
        $this->regenerateDB($this->getConnection());
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testAnonymizeUserTable()
    {
        $faker = FakerFactory::create();
        $targetFields[] = new TargetField('name', new FirstName(new Person($faker)));
        $targetFields[] = new TargetField('lastname', new LastName(new Person($faker)));
        $targetFields[] = new TargetField('birthdate', new DateTime('Y-m-d'));
        $targetFields[] = new TargetField('phone', new PhoneNumber(new FakerProviderPhoneNumber($faker)));
        $targets[] = new TargetTable('user', 'id', $targetFields);

        $connection = $this->getConnection();
        $anonymizer = new Anonymizer();
        $anonymizer->anonymize($connection, $targets);

        $selectStmt = $connection->prepare('SELECT `name`, `lastname`, `birthdate`, `phone` FROM `user`');
        $selectStmt->execute();
        while ($row = $selectStmt->fetch()) {
            $this->assertTrue(is_string($row['name']));
            $this->assertTrue(is_string($row['lastname']));
            $this->assertTrue(is_string($row['birthdate']));
            $this->assertTrue(is_string($row['phone']));
        }
    }
}
