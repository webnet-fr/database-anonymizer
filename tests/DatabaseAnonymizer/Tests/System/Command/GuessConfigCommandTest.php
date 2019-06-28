<?php

namespace DatabaseAnonymizer\Tests\System\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use WebnetFr\DatabaseAnonymizer\Command\GuessConfigCommand;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesser;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigWriter;
use WebnetFr\DatabaseAnonymizer\Tests\System\SystemTestTrait;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class GuessConfigCommandTest extends TestCase
{
    use SystemTestTrait;

    const EXPECTED = <<<EOF
webnet_fr_database_anonymizer:
    tables:
        orders:
            fields:
                address:
                    generator: faker
                    formatter: streetAddress
                street_address:
                    generator: faker
                    formatter: streetAddress
                zip_code:
                    generator: faker
                    formatter: postcode
                city:
                    generator: faker
                    formatter: city
                country:
                    generator: faker
                    formatter: country
                comment:
                    generator: faker
                    formatter: realText
                    arguments: [200, 2]
        users:
            fields:
                email:
                    generator: faker
                    formatter: safeEmail
                firstname:
                    generator: faker
                    formatter: firstName
                    arguments: [null]
                lastname:
                    generator: faker
                    formatter: lastName
                birthdate:
                    generator: faker
                    formatter: dateTimeBetween
                    arguments: ['-30 years', now, null]
                    date_format: 'Y-m-d H:i:s'
                phone:
                    generator: faker
                    formatter: phoneNumber
                password:
                    generator: faker
                    formatter: password

EOF;

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
        $commandTester = $this->doExecute();

        $this->assertEquals(self::EXPECTED, $commandTester->getDisplay());
    }

    public function testExecuteFile()
    {
        $file = sys_get_temp_dir().'/'.uniqid('', true);

        $this->doExecute([
            '--file' => $file,
        ]);

        $this->assertEquals(self::EXPECTED, file_get_contents($file));
    }

    /**
     * @param array $input
     *
     * @return CommandTester
     */
    private function doExecute($input = [])
    {
        $configGuesser = new ConfigGuesser();
        $configWriter = new ConfigWriter();

        $command = (new Application('Database anonymizer', '0.0.1'))
            ->add(new GuessConfigCommand($configGuesser, $configWriter));

        $commandTester = new CommandTester($command);

        $input = array_merge(
            [
                'command' => $command->getName(),
                '--type' => $GLOBALS['db_type'],
                '--host' => $GLOBALS['db_host'],
                '--port' => $GLOBALS['db_port'],
                '--database' => $GLOBALS['db_name'],
                '--user' => $GLOBALS['db_username'],
                '--password' => $GLOBALS['db_password'],
            ],
            $input
        );

        $commandTester->execute($input);

        return $commandTester;
    }
}
