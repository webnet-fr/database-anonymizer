<?php

namespace DatabaseAnonymizer\Tests\Unit\ConfigGuesser;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use PHPUnit\Framework\TestCase;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesser;
use WebnetFr\DatabaseAnonymizer\ConfigGuesser\ConfigGuesserHint;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
class ConfigGuesserTest extends TestCase
{
    const TO_SNAKE_CASE_TESTS = [
        'snake_case'          => 'snake_case',
        'camelCase'           => 'camel_case',
        'lowercase'           => 'lowercase',
        'UPPERCASE'           => 'uppercase',
        'StartsWithCapital'   => 'starts_with_capital',
        'abbrLAST'            => 'abbr_last',
        'ABBRFirst'           => 'abbr_first',
        'abbrINTHEMiddle'     => 'abbr_inthe_middle',
        'SCREAMING_CASE_HERE' => 'screaming_case_here',
    ];

    public function testToSnakeCase()
    {
        $guesser = new ConfigGuesser();
        $reflConfigGuesser = new \ReflectionObject($guesser);
        $reflMethod = $reflConfigGuesser->getMethod('toSnakeCase');
        $reflMethod->setAccessible(true);

        foreach (self::TO_SNAKE_CASE_TESTS as $test => $expected) {
            $this->assertEquals(
                $expected,
                $reflMethod->invoke($guesser, $test)
            );
        }
    }

    public function testGuess()
    {
        $firstNameColumn = $this->createMock(Column::class);
        $firstNameColumn->method('getName')->willReturn('firstname');

        $usernameColumn = $this->createMock(Column::class);
        $usernameColumn->method('getName')->willReturn('user_name');

        $table = $this->createMock(Table::class);
        $table->method('getName')->willReturn('users');
        $table->method('getColumns')->willReturn([$usernameColumn, $firstNameColumn]);

        $schemaManager = $this->createMock(AbstractSchemaManager::class);
        $schemaManager->method('listTables')->willReturn([$table]);

        $connection = $this->createMock(Connection::class);
        $connection->method('getSchemaManager')->willReturn($schemaManager);

        $config = ConfigGuesser::guess($connection);
        $this->assertTrue(is_array($config));
        $this->assertTrue(isset($config['users']['user_name']));
        $this->assertInstanceOf(ConfigGuesserHint::class, $config['users']['user_name']);
        $this->assertEquals('userName', $config['users']['user_name']->formatter);
        $this->assertTrue($config['users']['user_name']->unique);

        $this->assertTrue(isset($config['users']['firstname']));
        $this->assertInstanceOf(ConfigGuesserHint::class, $config['users']['firstname']);
        $this->assertEquals('firstName', $config['users']['firstname']->formatter);
    }

    public function testGuessZipCode()
    {
        $hint = ConfigGuesser::guessColumn('zip_code');
        $this->assertEquals('postcode', $hint->formatter);
    }

    public function testPostCode()
    {
        $hint = ConfigGuesser::guessColumn('postCode');
        $this->assertEquals('postcode', $hint->formatter);
    }

    public function testGuessCodePostal()
    {
        $hint = ConfigGuesser::guessColumn('code_postal');
        $this->assertEquals('postcode', $hint->formatter);
        $this->assertEquals('fr_FR', $hint->locale);

        $hint = ConfigGuesser::guessColumn('CP');
        $this->assertEquals('postcode', $hint->formatter);
        $this->assertEquals('fr_FR', $hint->locale);
    }
}
