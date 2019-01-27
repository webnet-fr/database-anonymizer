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
     */
    protected function setUp()
    {
        $this->regenerateDB($this->getConnection());
    }

    public function testExecute()
    {
        $command = (new Application('Database anonymizer', '0.0.1'))
            ->add(new AnonymizeCommand());

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(array('y'));
        $commandTester->execute([
            'command' => $command->getName(),
            'config' => realpath('tests/config/config.yaml'),
            'db_url' => getenv('db_url'),
        ]);

        $connection = $this->getConnection();

        $selectStmt = $connection->prepare('SELECT `name`, `lastname`, `birthdate`, `phone` FROM `user`');
        $selectStmt->execute();
        while ($row = $selectStmt->fetch()) {
            $this->assertTrue(is_string($row['name']));
            $this->assertTrue(is_string($row['lastname']));
            $this->assertTrue(is_string($row['birthdate']));
            $this->assertTrue(is_string($row['phone']));
        }

        $selectStmt = $connection->prepare('SELECT `date` FROM `order`');
        $selectStmt->execute();

        while ($row = $selectStmt->fetch()) {
            $this->assertTrue(is_string($row['date']));
        }
    }
}
