<?php

namespace WebnetFr\DatabaseAnonymizer\Tests\System\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WebnetFr\DatabaseAnonymizer\Command\AnonymizeCommand;
use WebnetFr\DatabaseAnonymizer\Tests\System\SystemTestTrait;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class AnonymizeCommandTest extends TestCase
{
    use SystemTestTrait;

    /**
     * @inheritdoc
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp()
    {
        $this->regenerateUsersOrders();
    }

    public function testExecute()
    {
        $command = (new Application('Database anonymizer', '0.0.1'))
            ->add(new AnonymizeCommand());

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(array('y'));
        $commandTester->execute([
            'command' => $command->getName(),
            'config' => realpath('../config/config.yaml'),
            '--type' => $GLOBALS['db_type'],
            '--host' => $GLOBALS['db_host'],
            '--port' => $GLOBALS['db_port'],
            '--database' => $GLOBALS['db_name'],
            '--user' => $GLOBALS['db_username'],
            '--password' => $GLOBALS['db_password'],
        ]);

        $connection = $this->getConnection();

        $selectStmt = $connection->prepare('SELECT `email`, `firstname`, `lastname`, `birthdate`, `phone`, `password` FROM `user`');
        $selectStmt->execute();
        while ($row = $selectStmt->fetch()) {
            $this->assertTrue(is_string($row['email']));
            $this->assertTrue(is_string($row['firstname']));
            $this->assertTrue(is_string($row['lastname']));
            $this->assertTrue(is_string($row['birthdate']));
            $this->assertTrue(is_string($row['phone']));
            $this->assertTrue(is_string($row['password']));
        }

        $selectStmt = $connection->prepare('SELECT `address`, `street_address`, `zip_code`, `city`, `country`, `comment`, `comment`, `created_at` FROM `order`');
        $selectStmt->execute();

        while ($row = $selectStmt->fetch()) {
            $this->assertTrue(is_string($row['address']));
            $this->assertTrue(is_string($row['street_address']));
            $this->assertTrue(is_string($row['zip_code']));
            $this->assertTrue(is_string($row['city']));
            $this->assertTrue(is_string($row['country']));
            $this->assertTrue(is_string($row['comment']));
            $this->assertTrue(is_string($row['created_at']));
        }
    }
}
